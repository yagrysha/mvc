<?php
/**
 * @author Yaroslav Gryshanovich <yagrysha@gmail.com>
 * todo
 */

namespace Yagrysha\MVC;

abstract class Form implements \ArrayAccess
{

    protected $request;
    /**
     * form fields
     * @var array
     */
    protected $fields;
    /**
     *
     * @var string
     */
    protected $error;
    protected $key;
    protected $sessionkey = __CLASS__;
    /** @link http://ar2.php.net/manual/ru/filter.filters.php
     * @var array [$name, $flag, $options, $message]
     */
    protected $filters;
    protected $csrf = true;
    protected $csrf_message = 'Wrong form key';
    const F_IF_NOTEMPTY = 'notempty';//FLAG apply filter if field not empty
    const F_IF_EMPTY = 'ifempty';//FLAG if empty
    const F_METHOD = 'method';//FLAG method

    protected $item; //defaults

    /**
     *
     * @param array $default дефолтные значения полей
     */
    public function __construct($default = null)
    {
        $this->filters = $this->filters();
        $this->item = $default;
        if (!empty($default)) {
            $this->setDefaults($default);
        }
    }

    /**
     * @param ArrayAccess|array $defaults
     */
    protected function setDefaults($defaults)
    {
        $keys = array_keys($this->fields);
        foreach ($keys as $k) {
            if (isset($defaults[$k])){
                $this->fields[$k] = $defaults[$k];
            }
        }
    }

    /**
     * @return string
     */
    public function getKey()
    {
        $this->key = md5(uniqid($this->key, true));
        $_SESSION[$this->sessionkey] = $this->key;
        return $this->key;
    }

    /**
     * проверяет вхоные данные, выполняет действие
     * @param $request
     * @return bool
     */
    public function process($request)
    {
        $this->request = $request;
        if ($this->csrf
            && empty($this->request['formkey'])
            || $this->request['formkey'] != $_SESSION[$this->sessionkey]
        ) {
            $this->error = $this->csrf_message;
            return false;
        }
        $this->setFieldsFromRequest();
        $filters = $this->getFilters();
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if (!$this->applyFilter($filter)) {
                    return false;
                }
            }
        }
        if (!$this->checkRequest()) {
            return false;
        }
        return $this->doAction();
    }

    protected function checkRequest()
    {
        return true;
    }

    protected function filters()
    {
        return $this->filters;
    }

    abstract protected function doAction();

    /**
     * return is empty error message
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * get error message
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * параметры формы
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     *
     */
    protected function setFieldsFromRequest()
    {
        foreach ($this->fields as $k => $v) {
            if (isset($this->request[$k])) {
                $this->fields[$k] = $this->request[$k];
            }
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function setField($name, $value)
    {
        $this->fields[$name] = $value;
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (isset($this->fields[$offset]))
            $this->fields[$offset] = $value;
    }

    /**
     * ниделает ничего. удалять занчения нельзя
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     *
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * обработчик фильтра входящих данных
     * [
     *      [name|[name]|*, flag|[flag], [options]|flags, message ]
     * ]
     * @param $filter
     * @return bool
     */
    protected function applyFilter($filter)
    {
        @list($name, $flag, $options, $message) = $filter;
        if ($name == '*') {
            $name = array_keys($this->fields);
        }
        if (is_array($name)) {
            foreach ($name as $n) {
                return $this->applyFilter([$n, $flag, $options, $message]);
            }
        } elseif (is_array($flag)) {
            foreach ($flag as $f) {
                return $this->applyFilter([$name, $f, $options, $message]);
            }
        } elseif (self::F_IF_EMPTY == $flag) {
            if (empty($this->fields[$name])) {
                $this->error = empty($message) ? $options : $message;
                return false;
            }
        } elseif (self::F_IF_NOTEMPTY == $flag) {
            if (!empty($this->fields[$name])) {
                $this->error = empty($message) ? $options : $message;
                return false;
            }
        } else {
            if (self::F_METHOD == $flag) {
                $var = $this->$options($this->fields[$name]);
            } else {
                $var = filter_var($this->fields[$name], $flag, $options);
            }
            if ($var === false && !empty($message)) {
                $this->error = $message;
                return false;
            }
            $this->fields[$name] = $var;
        }
        return true;
    }

    protected function getFilters()
    {
        return $this->filters;
    }
}