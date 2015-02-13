<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'odm_default' => array(
                'server'    => 'localhost',
                'port'      => '27017',
                'dbname'    => 'zf2app',
                'options'   => array()
            ),
        ),
        'configuration' => array(
            'odm_default' => array(
                'metadata_cache'     => 'array',
                'driver'             => 'odm_default',
                'generate_proxies'   => true,
                'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
                'generate_hydrators' => true,
                'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
                'default_db'         => 'zf2app',
                'filters'            => array()
            )
        ),
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'App\Document' => 'aplikasi'
                )
            ),
            'aplikasi' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'module/App/src/App/Document'
                )
            )
        ),
        'documentmanager' => array(
            'odm_default' => array(
                'connection'    => 'odm_default',
                'configuration' => 'odm_default',
                'eventmanager' => 'odm_default'
            )
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array()
            )
        ),
    ),
);