<?php

namespace Yagrysha\MVC;

//TODO не готов
abstract class Pager {

    protected $curpage = 1;
    protected $filter;
    protected $count;
    protected $onpage = 10;
    protected $filtersession;
    protected $error;
    protected $numberpages = 10;
    protected $previous = '&lt;';
    protected $next = '&gt;';

    /**
     * @param mixed $filtersession
     */
    public function setFiltersession($filtersession)
    {
        $this->filtersession = $filtersession;
    }

    /**
     *
     * @param int $page current page number
     */
    public function __construct($page = null) {
        if (!empty($page))
            $this->setCurpage($page);
        if (isset($_SESSION[$this->filtersession])){
            $this->setFilter($_SESSION[$this->filtersession]);
        }
    }

    /**
     * set curren page number
     * @param int $page
     */
    public function setCurpage($page) {
        $this->curpage = $page;
    }

    /**
     *
     * @param int $amount
     */
    public function setOnpage($amount){
        $this->onpage=$amount;
    }

    /**
     * get number all items
     * @return int
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * return is empty error message
     * @return bool
     */
    public function hasError() {
        return !empty($this->error);
    }

    /**
     * get error message
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * set filter parameters
     * @param array $filter
     */
    public function setFilter($filter) {
        if (is_null($filter))
            unset($_SESSION[$this->filtersession]);
        $this->filter = $filter;
        $_SESSION[$this->filtersession] = $filter;
    }

    /**
     * get filter parameters
     * @return type
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * retrun isset
     * @param string $name
     * @return bool
     */
    public function inFilter($name) {
        return isset($this->filter[$name]);
    }

    /**
     * set filter ti Null
     */
    public function remFilter() {
        $this->setFilter(null);
    }

    /**
     * массив номеров старниц
     * @return array
     */
    public function getPager() {
        if ($this->curpage < 2 && $this->count <= $this->onpage)
            return false;
        return $this->getPagesArray();
    }

    /**
     * set filter param
     * @param string $name
     * @param mixed $value
     */
    public function setFilterParam($name, $value) {
        if (is_null($value)) {
            unset($this->filter[$name]);
            unset($_SESSION[$this->filtersession][$name]);
        } else {
            $this->filter[$name] = $value;
            $_SESSION[$this->filtersession][$name] = $value;
        }
    }
    public function getFilterParam($name){
        return isset($this->filter[$name])?$this->filter[$name]:null;
    }

    /**
     * удаление параметра фильтра
     * @param string $name
     */
    public function remFilterParam($name) {
        $this->setFilterParam($name, null);
    }

    /**
     * сообщение Найдено x реультатов
     * @return string
     */
    public function getCountMes() {
        return Utils::getDigesd($this->count, 'Найден ', 'Найдено ', 'Найдено ', false) .
        Utils::getDigesd($this->count, 'результат', 'результата', 'результатов');
    }

    /**
     * @return array
     */
    abstract public function getItems();

    /**
     * взято их utils/ старая хренотень
     * @return array
     */
    public function getPagesArray() {
        $lastpage = ceil($this->count / $this->onpage);
        $end = ceil($this->curpage / $this->numberpages) * $this->numberpages;
        $start = $end - ($this->numberpages - 1);
        $end = ($end > $lastpage) ? $lastpage : $end;
        $pages = array();
        if ($start > 1)
            $pages[$start - 1] = $this->previous;
        for ($i = $start; $i <= $end; $i++) {
            $pages[$i] = $i;
        }
        if ($end < $lastpage)
            $pages[$end + 1] = $this->next;
        return $pages;
    }
}