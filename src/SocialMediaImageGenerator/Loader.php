<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator;

class Loader
{

    public function __construct(array $data = [])
    {
        foreach ($data as $func => $value) {
            $func_name = $this->getFuncByString($func, 'set');

            if (\method_exists($this, $func_name)) {
                $class = new \ReflectionMethod($this, $func_name);

                if ($class->getNumberOfParameters() > 1) {
                    call_user_func_array([$this, $func_name], $value);
                } else {
                    $this->{$func_name}($value);
                }

            }
        }
    }

    public function getFuncByString(string $field, string $prefix = '')
    {
        return sprintf('%s%s', $prefix, implode('', array_map('ucfirst', explode('_', $field))));
    }

}