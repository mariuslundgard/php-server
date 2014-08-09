<?php
    
namespace Server;

use Util\Dictionary;

class View
{
    protected $path;
    protected $data;

    public function __construct($path)
    {
        $this->path = $path;
        $this->data = new Dictionary();
    }

    public function render(array $data = array(), $includePath = null)
    {
        if (! file_exists($this->path)) {
            throw new Error('The file does not exist: ' . $this->path);
        }

        // $buffer = ob_get_clean();

        extract($data + $this->data->get());
        
        ob_start();
        
        if ($includePath) {
            $oldIncludePath = set_include_path($includePath);
        }

        include $this->path;
        
        if ($includePath) {
            set_include_path($oldIncludePath);
        }

        $output = ob_get_clean();

        // echo $buffer;

        return $output;
    }

    public static function create($path)
    {
        return new static($path);
    }
}
