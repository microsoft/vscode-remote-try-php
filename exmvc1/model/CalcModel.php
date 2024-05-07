class CalcModel
{
    public $result;
    public $a;
    public $b;
    
    public function method_calc($method)
    {
        switch ($method)
        {
            case 'add':
                $this->result = $this->a + $this->b;
                break;
            case 'sub':
                $this->result = $this->a - $this->b;
                break;
            default:
                $this->result = "No support";
                break;
        }
    }
}
