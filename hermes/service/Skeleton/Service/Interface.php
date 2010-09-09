<?php

interface Skeleton_Service_Interface
{
    public function getDescriptor();
    
    public function callMethod($methodDescriptor, $rpcController, $request, $callback);
    
    public function getRequestClass($methodDescriptor);
    
    public function getResponseClass($methodDescriptor);
}

?>