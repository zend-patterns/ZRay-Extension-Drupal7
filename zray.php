<?php

$zre = new \ZRayExtension('drupal');

$zre->traceFunction('module_invoke', function(){}, function($context, & $storage){
	$module = $context['functionArgs'][0];
	$action = $context['functionArgs'][1];
	$hook = isset($context['functionArgs'][2]) ? $context['functionArgs'][2] : '';
	
	$storage['moduleInvoke'] = array('module' => $module, 'action' => $action, 'hook' => $hook);
});

$zre->traceFunction('drupal_load', function(){}, function($context, & $storage){
 	$module = $context['functionArgs'][1];
	
	$storage['LoadedModules'] = array('module' => $module);
});

$zre->traceFunction('call_user_func', function(){}, function($context, & $storage){
	$called = $context['functionArgs'][0];
	$parameter = isset($context['functionArgs'][1]) ? $context['functionArgs'][1] : '';
	$blob = isset($context['functionArgs'][2]) ? json_encode($context['functionArgs'][2]) : '';
	
 	$storage['CalledFunctions'] = array('called' => $called, 'parameter' => $parameter, 'info' => $blob);
});

$zre->traceFunction('menu_execute_active_handler', function(){}, function($context, & $storage) {
	global $user;
	
	$stateUser = (array)$user;
	$stateUserKeys = array_keys($stateUser);
	
	$stateUser['roles'] = array_reduce($stateUser['roles'], function($reduced, $item){
		if ($reduced == '') {
			$reduced = $item;
		} else {
			$reduced .= ", $item";
		}
		return $reduced;
	});
	
	$stateUserValues = array_map(function($key) use ($stateUser) {
		return array('property' => $key, 'value' => $stateUser[$key]);
	}, $stateUserKeys);
	
	
	
	$storage['userProperties'] = $stateUserValues;
});

$zre->traceFunction('drupal_retrieve_form', function(){}, function($context, & $storage) {

	$formName = $context['functionArgs'][0];
 	$formId = $context['functionArgs'][1]['build_info']['form_id'];
 	$baseFormId = $context['functionArgs'][1]['build_info']['base_form_id'];
 	$cache = $context['functionArgs'][1]['cache'];
 	$method = $context['functionArgs'][1]['method'];
 	$activity = 'Display';
 	
 	if ($context['functionArgs'][1]['executed']) {
 		$activity = 'Executed';
 	} elseif ($context['functionArgs'][1]['programmed']) {
 		$activity = 'Programmed';
 	} elseif ($context['functionArgs'][1]['rebuild']) {
 		$activity = 'Rebuilt';
 	} elseif ($context['functionArgs'][1]['rebuild']) {
 		$activity = 'Rebuilt';
 	}
 	
 	$storage['RetrievedForms'] = array(
 			'formName' => $formName, 'formId' => $formId, 'baseFormId' => $baseFormId,
 			'cache' => $cache, 'method' => $method, 'activity' => $activity
 	);
});

