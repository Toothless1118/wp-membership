<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  "get_{$this->class_info->plural}" => (object)array(
    'name'   => sprintf(__('Get %s', 'memberpress-developer-tools'), $mpdt_inflector->humanize($this->class_info->plural)),
    'desc'   => __('', 'memberpress-developer-tools'),
    'method' => 'GET',
    'url'    => rest_url($this->namespace.'/'.$this->base),
    'auth'   => true,
    'search_args'  => $this->search_fields,
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => false,
      'count' => 10
    )
  ),
  "get_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Get %s', 'memberpress-developer-tools'), $mpdt_inflector->humanize($this->class_info->singular)),
    'desc'   => __('', 'memberpress-developer-tools'),
    'method' => 'GET',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "create_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Create %s', 'memberpress-developer-tools'), $mpdt_inflector->humanize($this->class_info->singular)),
    'desc'   => __('', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base),
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => $this->accept_fields,
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "update_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Update %s', 'memberpress-developer-tools'), $mpdt_inflector->humanize($this->class_info->singular)),
    'desc'   => __('', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => $this->accept_fields,
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "delete_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Delete %s', 'memberpress-developer-tools'), $mpdt_inflector->humanize($this->class_info->singular)),
    'desc'   => __('', 'memberpress-developer-tools'),
    'method' => 'DELETE',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  )
);

