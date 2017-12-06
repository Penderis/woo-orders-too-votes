<?php
namespace Purpose\Classes;


use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Dotenv\Dotenv;



class WooApiConnect extends Client {
	
	
	public $theClient;
    public $resultsList;
    public $newResults;
	public $defaultMulti = 1;
	
	//protected $currentYear = '2017';
	
	//format d-m-Y
	protected $dateMultipliers = [
	"27-10-2017"=>2,
	"17-10-2017"=>2,
	"07-11-2017"=>2,
	"14-11-2017"=>2,
	"24-11-2017"=>6,
	"25-11-2017"=>6,
	"26-11-2017"=>6,
	];
	
	public $key='voting-points';
	
	public function __construct($url,array $options = []){

	    //Get environment keys - this is so far the only place it is used , so fuckit
        //will figure out how the fuck to add this to the SLIM APP
        $dotenv = new Dotenv(__DIR__."/../../");
        $dotenv->load();

		/*
         * signature for wooclient
            url
            consumer key
            private key
            options - array
        */
		
		$this->theClient = new Client(
		$url,
		getenv("Consumer_Key"),
		getenv("Consumer_Secret"),
		$options
		);
		
		
		return $this->theClient;
		
	}

    public function cmp($a, $b)
    {

        return $b["grandtotal"]- $a["grandtotal"];

    }
	
	public function add($item, $key){
		
		return $item + $item;
		
	}
	
	
	public function removeNumDash ($string){
		
		//remove variation numbers and dash from names to use as unique key
		return trim(preg_replace('/[0-9-]/i',"",$string));
		
	}
	
	
	public function createdmY ($date){
		
		$dateCreated = new \DateTime($date);
		
		return $dateCreated->format('d-m-Y');
		
    }

    public function createYear ($date){

        $dateCreated = new \DateTime($date);
		
		return $dateCreated->format('Y');

    }

    public function createMonth ($date){

        $dateCreated = new \DateTime($date);
		
		return $dateCreated->format('m');

    }
    
    public function returnKeyValue($item){
        //reliant on returned array structure inside foreach loop
        if($item['meta_data'][0]['key'] == $this->key){
            
            return (int) $item['meta_data'][0]['value'];
            
        }
    }
    
    public function buildResults($results){

        foreach ($results as $result){
            //create date keys
            $dateCreatedY = $this->createYear($result['date_created']);
            $dateCreatedM = $this->createMonth($result['date_created']);
            $dateCreatedF = $this->createdmY($result['date_created']);

            if(!array_key_exists($dateCreatedY)){
                $this->newResults[$dateCreatedY];
            }
        }

        //dumps
        echo "full array <br>";
        var_dump($results);
        echo "single <br>";
        var_dump($results[0]);
        echo "individual contents";
        var_dump($results[0]["_links"]["self"]);
        echo "New Array";
        var_dump($this->newResult);

    }
	
	public function sortResults($results){
		
		foreach ($results as $result) {
			
			
			$multiplier = 1;
			
			$value=0;
			
			
			//create date time from order id
			$dateCreated = $this->createdmY($result['date_created']);
			
			//if date matches a date with a multiplier , assign multiplier value
			if (array_key_exists($dateCreated, $this->dateMultipliers)) {
				
				//var_dump($this->dateMultipliers[$dateCreated]);
				
				$multiplier = $this->dateMultipliers[$dateCreated];
				
			}
			else {
				
				//echo "No Key";
				
			}
			
			//var_dump($dateCreated);
			
			//remove variation numbers and dash from names to use as unique key
			foreach($result['line_items'] as $item){
				
				$replace = $this->removeNumDash($item['name']);
				
				//create item array
				//$this->resultsList[$replace] ;
				
				//var_dump($item);
				
				
				//if variation key is voting-points then get the value of the points
				$value = $this->returnKeyValue;
				
				
				//take value and multiply with multiplier
				$sum= ((int) $value * (int) $multiplier)*(int) $item['quantity'];
				
				//var_dump($sum);
				
				$this->resultsList[$replace]['totals'][$dateCreated][]=$sum;
				
				
				//var_dump($replace);
				
				//var_dump($dateCreated);
				
				//var_dump($sum);
				
			}
			
			
			
			
		}
		;
		
		//Dump origin results
		//print_r($results);
		
		//var_dump($results);
		
		//dump full new list
		//var_dump($this->resultsList);
		
		foreach($this->resultsList as $keys =>$item){
			
			$grandtotal = 0;
			
			foreach($item['totals'] as $dateof => $value){
				
				// 				echo "dates arrays";
				
				// 				var_dump($dateof);
				
				foreach ($value as $itemized) {
					
					$grandtotal += (int) $itemized;
					
					// 					var_dump($itemized);
					
				}
				
			}
			
			$this->resultsList[$keys]['grandtotal']= $grandtotal;
			
			// 			echo "items";
			
			// 			var_dump($item);
			
		}
		
		
		
		foreach($this->resultsList as $keys =>$item){
			
			
			//v			ar_dump($keys);
			
			// 			var_dump($item);
			
		}
		
		var_dump($this->resultsList);
		
		
		
		
		uasort($this->resultsList, array($this,"cmp"));
		
		//v		ar_dump($this->resultsList);
		
		return $this->resultsList;
		
	}
	
}
