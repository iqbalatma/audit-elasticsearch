# Audit Log Elasticsearch
This is a package developed specific for project Pilpres Timnas Anis-Muhaimin. The main goal of this project is to record log of business process. We have a sql table to record every changes of business process then at some period of time we will sync the record into elasticsearch to make it easier to search. 
We also provide command for pruning database and command for re-sync data from sql into elasticsearch.


## How to install ?
Here is how to install audit elasticsearch with composer
```
composer require iqbalatma/audit-elasticsearch
```
