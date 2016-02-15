<?php

/**
 * UnitTest short summary.
 *
 * UnitTest description.
 *
 * @version 1.0
 * @author Romain
 */
class UnitTest
{
    public function __construct($name, $depends, $test)
    {
        $this->name=$name;
        $this->depends=$depends;
        $this->test=$test;
        $this->reset();
    }
    
    private $State;
    private $Result;
    
    /**
     * @var string
     */
    private $name;
    /**
     * @var UnitTest[]
     */
    private $depends;
    
    /**
     * @var callable
     */
    private $test;
    
    private function reset()
    {
        $this->State="NOTRUNNED";
        $this->Result=null;
        foreach($this->depends as $depend)
        {
            $depend->reset();
        }
    }
    
    
    private function internRunCheck()
    {   
        switch($this->State)
        {
            case "NOTRUNNED":
                {
                    try{
                            $Params=[];
                            foreach($this->depends as $depend)
                            {
                                    try{
                                            $Params[]=$depend->internRunCheck();
                                        }
                                    catch(Exception $ex)
                                    {
                                        throw new ErrorException ("Dependence échoué : ".$depend->name);
                                    }
                                }
                            $this->Result = call_user_func_array($this->test,$Params);
                            $this->State = "SUCCESS";
                            return $this->Result;
                        }
                    catch(Exception $ex)
                    {
                        $this->State = "FAILLED";
                        $this->Result = $ex;
                        throw $this->Result;
                    }
                } 
            case "SUCCESS":
                return $this->Result;
            case  "FAILLED":
                throw $this->Result; 
        }
        
    }
    public function RunCheck()
    {
        $this->reset();
        $startDate=microtime();
        try{
            $this->internRunCheck();
            $DiffDate = microtime()-$startDate;
            echo "[PASSED] ".$this->name." in ".(string)$DiffDate."ms\n";
        }
        catch(Exception $ex)
        {
            echo "[FAILED] ".$this->name." with \"".$ex->getMessage()."\"\n";
        }
    }
    
}