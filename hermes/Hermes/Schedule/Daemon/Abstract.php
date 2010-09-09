<?php

/**
 * MultiThreaded Daemon (MTD)
 * 
 * Copyright (c) 2007, Benoit Perroud
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met: Redistributions of source code must retain the
 * above copyright notice, this list of conditions and the following
 * disclaimer. Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following disclaimer
 * in the documentation and/or other materials provided with the
 * distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     MTD
 * @author      Benoit Perroud <ben@migtechnology.ch>
 * @copyright   2007 Benoit Perroud
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     $Id: class.MTDaemon.php 8 2007-10-19 09:16:00Z killerwhile $
 *
 * See http://code.google.com/p/phpmultithreadeddaemon/ 
 * and http://phpmultithreaddaemon.blogspot.com/ for more information
 *
 */

require_once 'Hermes/Schedule/Daemon/Debugger.php';

abstract class Hermes_Schedule_Daemon_Abstract
{

    /*
     * Configuration vaiables
     */

    // max concurrent threads
    protected $max_threads = 4; // should be implemented with sem_get('name', $max_aquire) but this can't be dynamically updated as this var.

    // sleep time when no job
    protected $idle_sleep_time = 5; 

    /*
     * Internal constants
     */
    const _INDEX_DATA = 0;    // shared data goes here.
    const _INDEX_THREADS = 1; // how many threads are working
    const _INDEX_SLOTS = 2;   // which slot do the current thread use ?

    /*
     * Internal variables
     */
    protected $shm;                         // = ftok(__FILE__, 'g');
    protected $shared_data;                 // = shm_attach($this->shm);

    protected $mutex;                       // lock critical path, used in lock() and unlock()
    protected $mutex_main_process;          // lock main process only. Children can continue to run
    protected $mutex_children_processes;    // lock children processes only. Main can continue to run

    protected $main_thread_pid;

    /*
     * Constructor
     * 
     * @params $threads : number of concurrent threads, default 4
     * @params $idelsleeptime : time to sleep when no job ready (getNext return null), in seconds, default 5 
     */
    public function __construct($threads = null, $idlesleeptime = null)
    {
        if ($threads) $this->max_threads = $threads;
        if ($idlesleeptime) $this->idle_sleep_time = $idlesleeptime;
        $this->main_thread_pid = posix_getpid();
        $this->_debugger = Hermes_Schedule_Daemon_Debugger::getInstance();
    }
     
    public function getDebugger()
    {
        return $this->_debugger;
    }

    /*
     * Hook called just before the main loop.
     * 
     * Remark : cleanup code goes here.
     */
    protected function _prerun()
    {
        $this->getDebugger()->info('Starting daemon with ' . $this->max_threads . ' slots');

        $this->shm                      = ftok(__FILE__, 'g'); // global shm
        $this->shared_data              = shm_attach($this->shm);

        $this->mutex                    = sem_get($this->shm);
        $this->mutex_main_process       = sem_get(ftok(__FILE__, 'm'));
        $this->mutex_children_processes = sem_get(ftok(__FILE__, 'c'));

        shm_put_var($this->shared_data, self::_INDEX_DATA, array());

        $this->setThreads(0);

        $slots = array();
        for ($i = 0; $i < $this->max_threads; $i++) {
            $slots[] = false;
        }
        shm_put_var($this->shared_data, self::_INDEX_SLOTS, $slots);
    }

    /*
     * Hook called just after the main loop
     */
    protected function _postrun()
    {
        $this->getDebugger()->info('Stopping daemon. ');

        shm_remove($this->shared_data);
        sem_remove($this->mutex);
        sem_remove($this->mutex_process);
    }

    /*
     * Main loop, request next job using getNext() and execute run($job) in a separate thread
     * _prerun and _postrun hooks are called before and after the main loop -> usefull for cleanup and so on.
     */
    public function handle()
    {

        $this->run = true;

        $this->_prerun();

        while ($this->run) {

            /* 
             * Terminating all child, to not let some zombie leaking the memory.
             */

            $this->getDebugger()->debug2('-- Next iteration ');

            $this->lock();

            // HACK : avoid zombie and free earlier the memory
            do {
                $res = pcntl_wait($status, WNOHANG);
                $this->getDebugger()->debug2('$res = pcntl_wait($status, WNOHANG); called with $res = ' . $res);
                if ($res > 0) $this->getDebugger()->debug('(finishing child with pid ' . $res . ')');
            } while ($res > 0);

            /*
             * Loop until a slot frees 
             */
            while (!$this->hasFreeSlot()) {
                $this->unlock();
                $this->getDebugger()->debug('No more free slot, waiting');
                $res = pcntl_wait($status); // wait until a child ends up
                $this->getDebugger()->debug2('$res = pcntl_wait($status); called with $res = ' . $res);
                if ($res > 0) {
                    $this->getDebugger()->debug('Finishing child with pid ' . $res);
                } else {
                    $this->getDebugger()->error('Outch1, this souldn\'t happen. Verify your implementation ...');
                    $this->run = false;
                    continue;
                }
                $this->lock();
            }

            $slot = $this->requestSlot();
            $this->incThreads();

            $this->unlock();

            if ($slot === null) {
                var_dump(shm_get_var($this->shared_data, self::_INDEX_DATA));
                var_dump(shm_get_var($this->shared_data, self::_INDEX_THREADS));
                var_dump(shm_get_var($this->shared_data, self::_INDEX_SLOTS));

                $this->getDebugger()->error('Outch2, this souldn\'t happen. Verify your implementation ...');
                $this->run = false;
                continue;
            }
            
            /*
             * Request next action to handle
             */
            $next = $this->getNext($slot);
            
            /*
             * If no job
             */
            if (!$next) {

                $this->getDebugger()->debug('No job, sleeping at most ' . $this->idle_sleep_time . ' sec ... ');

// TODO : waiting for signal pushed into a queue when inserting a new job.

                $this->lock();
                $this->releaseSlot($slot);
                $this->decThreads();
                $this->unlock();

                sleep($this->idle_sleep_time);

                continue;
                
            } else { 

                $pid = pcntl_fork();

                if ($pid == -1) {
                        $this->getDebugger()->error('[fork] Duplication impossible');
                        $this->run = false;
                        continue;
                } else if ($pid) {

                        usleep(10); // HACK : give the hand to the child -> a simple way to better handle zombies

                        continue;
                } else {

                    $this->getDebugger()->debug('Executing thread #' . posix_getpid() . ' in slot ' . number_format($slot));

                    $res = $this->run($next, $slot);

                    $this->lock();
                    $this->releaseSlot($slot);
                    $this->decThreads();
                    $this->unlock();

                    exit($res);

                }
            }
        }

        $this->_postrun();

    }

    /*
     * Request data of the next element to run in a thread
     * 
     * return null or false if no job currently
     */
    abstract public function getNext($slot);

    /*
     * Process the element fetched by getNext in a new thread
     * 
     * return the exiting status of the thread
     */
    abstract public function run($next, $slot);

    /*
     *
     */
    protected function lock()
    {
        $this->getDebugger()->debug2('[lock] lock');
        $res = sem_acquire($this->mutex);
        if (!$res) exit(-1);
    }

    /*
     *
     */
    protected function unlock()
    {
        $this->getDebugger()->debug2('[lock] unlock');
        $res = sem_release($this->mutex);
        if (!$res) exit(-1);
    }

    /*
     *
     */
    protected function lockMain()
    {
        $this->getDebugger()->debug2('[lock] lock main process');
        $res = sem_acquire($this->mutex_main_process);
        if (!$res) exit(-1);
    }

    /*
     *
     */
    protected function unlockMain()
    {
        $this->getDebugger()->debug2('[lock] unlock main process');
        $res = sem_release($this->mutex_main_process);
        if (!$res) exit(-1);
    }

    /*
     *
     */
    protected function lockChildren()
    {
        $this->getDebugger()->debug2('[lock] lock children processes');
        $res = sem_acquire($this->mutex_children_processes);
        if (!$res) exit(-1);
    }

    /*
     *
     */
    protected function unlockChildren()
    {
        $this->getDebugger()->debug2('[lock] unlock children processes');
        $res = sem_release($this->mutex_children_processes);
        if (!$res) exit(-1);
    }

    /*
     * Get a shared var based on hash.
     *
     * Return null if the var doesn't exist.
     */
    protected function getVar($name, $lock = false)
    {
        if ($lock) $this->lock();
        $vars = shm_get_var($this->shared_data, self::_INDEX_DATA);
        $value = (isset($vars[$name])) ? $vars[$name] : null;
        if ($lock) $this->unlock();
        return $value;
    }

    /*
     * Set a shared var.
     * 
     * Remark : the var should be serialized.
     */
    protected function setVar($name, $value, $lock = false)
    {
        if ($lock) $this->lock();
        $vars = shm_get_var($this->shared_data, self::_INDEX_DATA);
        $vars[$name] = $value;
        $res = shm_put_var($this->shared_data, self::_INDEX_DATA, $vars);
        if ($lock) $this->unlock();
        return $res;
    }

    /*
     * Get the number of running threads
     */
    protected function getThreads($lock = false)
    {
        if ($lock) $this->lock();
        $res = shm_get_var($this->shared_data, self::_INDEX_THREADS);
        if ($lock) $this->unlock();
        return $res;
    }

    /*
     * Set the number of running threads
     */    
    protected function setThreads($threads, $lock = false)
    {
        if ($lock) $this->lock();
        $res = shm_put_var($this->shared_data, self::_INDEX_THREADS, $threads);
        if ($lock) $this->unlock();
        return $res;
    }

    /*
     * Increment the number of running threads
     */
    protected function incThreads($lock = false)
    {
        if ($lock) $this->lock();
        $threads = $this->getThreads();
        $res = shm_put_var($this->shared_data, self::_INDEX_THREADS, $threads + 1);
        $this->getDebugger()->debug('incThreads, $threads = ' . ($threads + 1));
        if ($lock) $this->unlock();
        return $res;
    }

    /*
     * Decrement the number of running threads
     */
    protected function decThreads($lock = false)
    {
        if ($lock) $this->lock();
        $threads = $this->getThreads();
        $res = shm_put_var($this->shared_data, self::_INDEX_THREADS, $threads - 1);
        $this->getDebugger()->debug('decThreads, $threads = ' . ($threads - 1));
        if ($lock) $this->unlock();
        return $res;
    }

    /*
     * Return true if any slot is free
     */
    protected function hasFreeSlot()
    {
        $threads = $this->getThreads();
        $res = ($threads < $this->max_threads) ? true : false;
        $this->getDebugger()->debug('Has free slot ? => #running threads = ' . $threads);
        return $res;
    }

    /*
     * Assign a free slot
     *
     * Return null if no free slot is available
     */
    protected function requestSlot($lock = false)
    {
        $this->getDebugger()->debug('Requesting slot ... ');
        $slot = null;
        if ($lock) $this->lock();
        $slots = shm_get_var($this->shared_data, self::_INDEX_SLOTS);
        for ($i = 0; $i < $this->max_threads; $i++) {
            if (!isset($slots[$i])) {
                $slots[$i] = true;
                $slot = $i;
                break;
            } else {
                if ($slots[$i] == false) {
                    $slots[$i] = true;
                    $slot = $i;
                    break;
                }
            }
        }
        shm_put_var($this->shared_data, self::_INDEX_SLOTS, $slots);
        if ($lock) $this->unlock();
        if (is_null($slot)) {
            $this->getDebugger()->debug('no free slots !!');
        } else {
            $this->getDebugger()->debug('slot ' . $slot . ' found.');
        }
        return $slot;
    }

    /*
     * Release given slot
     */
    protected function releaseSlot($slot, $lock = false) {
        if ($lock) $this->lock();
        $slots = shm_get_var($this->shared_data, self::_INDEX_SLOTS);
        $slots[$slot] = false;
        shm_put_var($this->shared_data, self::_INDEX_SLOTS, $slots);
        if ($lock) $this->unlock();
        $this->getDebugger()->debug('Releasing slot ' . $slot);
        return true;
    }

}

