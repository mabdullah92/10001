<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'App\Controller\App' => 'App\Controller\AppController' 
				) 
		),
		'router' => array (
				'routes' => array (
						'app' => array (
								'type' => 'segment',
								'options' => array (
										// Change this to something specific to your module
										'route' => '/app[/][:action][/:id]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												// Change this value to reflect the namespace in which
												// the controllers for your module are found
												'__NAMESPACE__' => 'App\Controller',
												'controller' => 'App',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										// This route is a sane default when developing a module;
										// as you solidify the routes for your module, however,
										// you may want to remove it and replace it with more
										// specific routes.
										'default' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/[:controller[/:action]]',
														'constraints' => array (
																'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						) 
				) 
		),
		'view_manager' => array (
				'template_path_stack' => array (
						'App' => __DIR__ . '/../view' 
				) 
		),
		'console' => array (
				'router' => array (
						'routes' => array (
								'list-users' => array (
										'options' => array (
												'route' => 'consumer',
												'defaults' => array (
														'controller' => 'App\Controller\App',
														'action' => 'cons' 
												) 
										) 
								)
								 
						)
						 
				) 
		)
		 
);