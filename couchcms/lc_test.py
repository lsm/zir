#-*- coding: utf-8 -*-
from libraries import lightcloud

LIGHT_CLOUD = { 
    'lookup1_A': [ '127.0.0.1:41201', '127.0.0.1:51201' ],
    'storage1_A': [ '127.0.0.1:44201', '127.0.0.1:54201' ]
}

lookup_nodes, storage_nodes = lightcloud.generate_nodes(LIGHT_CLOUD)
lightcloud.init(lookup_nodes, storage_nodes)

lightcloud.set("hello", "world")
#print lightcloud.get("hello")
#lightcloud.delete("hello")

#lightcloud.set("fluffis_killeds", 2)
print lightcloud.incr("fluffis_killeds", 10)
#print lightcloud.get("fluffis_killeds")

lightcloud.list_add("my_fixed_list", [1,2,3,"你好啊"])
print lightcloud.list_get("my_fixed_list")

