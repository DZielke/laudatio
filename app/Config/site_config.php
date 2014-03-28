<?php
$config = array (
  'SchemeConfig' => 
  array (
    'current_scheme' => 'SchemaX',
  ),
  'IndexConfig' => 
  array (
    'index_name' => 'laudatioX',
    'index_address' => 'www.my-address.org'
  ),
    'FedoraConfig' =>
        array(
            'fedora_address' => '0.0.0.0',
            'fedora_address_port' => '0.0.0.0:8080',
            'fedora_userpwd' => 'user:pw',
            'fedora_test_address' => '0.0.0.0',
            'fedora_test_address_port' => '0.0.0.0:8080'
        ),

    'PiwikConfig' =>
        array(
            'piwik_userpwd' => 'piwik_user:piwiki_pw'
        ),
    'HandleConfig' =>
        array(
            'handle_userpwd' => 'handle_user:handle_pw'
        )
);