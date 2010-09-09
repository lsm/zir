<?php

interface Skeleton_Transport_Interface
{
    public function reset();
    
    public function failed();
    
    public function errorText();
    
    public function startCancel();
    
    public function setFailed($reason);
    
    public function isCanceled();
    
    public function notifyOnCancel($callback);
}
?>