<?php

namespace orion\core;
use FFI\Exception;

/*
   * Base Controller
   * Loads the models and views
   */
class Controller
{
  protected $base_path;

  public function __construct($base_path) {
    $this->base_path = $base_path;
  }

  // Load model
  public function model($model)
  {
    // Require model file
    require_once $this->base_path . '/Models/' . $model . '.php';

    // Instantiate model
    return new $model();
  }

  public function render($file_path, $render_data = [])
  {
    $complete_file_path = $this->base_path . '/Views/' . $file_path;

    if (!file_exists($complete_file_path)) {
      throw new Exception("File not found: $file_path");
    }

    // Extract the render data variables into local variables
    extract($render_data);

    // Start output buffering
    ob_start();

    // Include the file to be rendered
    include($complete_file_path);

    // Get the rendered content
    $rendered_content = ob_get_clean();

    echo $rendered_content;
  }
}
