<?php
class Gene_Paginator_Mock extends Gene_Paginator
{
    public function getPerpage()
    {
        return $this->_perpage;
    }
    public function getTemplate()
    {
        return $this->_template;
    }
    public function getStyle()
    {
        return $this->_style;
    }
}
