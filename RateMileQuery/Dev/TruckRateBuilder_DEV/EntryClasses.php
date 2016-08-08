<?php

	require 'errorLogger.php';

   /**
	*   Abstract class for all Entries (Records) 
	*	For use with the file BNARATEP located in XL_RBNAL Library (currently only in JOELIB)
	*/
	abstract class Entry
	{
		//Trucking Data
		protected $orgCity;
		protected $orgState;
		protected $orgZip3A;
		protected $orgZip3B;
		protected $orgZip5A;
		protected $orgZip5B;
		protected $orgZip6A;
		protected $orgZip6B;
		protected $desCity;
		protected $desState;
		protected $desZip3A;
		protected $desZip3B;
		protected $desZip5A;
		protected $desZip5B;
		protected $desZip6A;
		protected $desZip6B;
		protected $entryRpm;
		protected $entryFlat;
		protected $entryMin;
		protected $entryFuel;
		protected $entryComments;
		protected $milesBegin;
		protected $milesEnd;

		//Constructor initializes all fields Type Casting as appropriate
		function __construct()
		{
			$this->orgCity   = "";
			$this->orgState  = "";
			$this->orgZip3A  = "";
			$this->orgZip3B  = "";
			$this->orgZip5A  = "";
			$this->orgZip5B  = "";
			$this->orgZip6A  = "";
			$this->orgZip6B  = "";
			$this->desCity   = "";
			$this->desState  = "";
			$this->desZip3A  = "";
			$this->desZip3B  = "";
			$this->desZip5A  = "";
			$this->desZip5B  = "";
			$this->desZip6A  = "";
			$this->desZip6B  = "";
			$this->entryRpm  = 0.0;
			$this->entryFlat = 0.0;
			$this->entryMin  = 0.0;
			$this->entryFuel = "";
			$this->entryComments = " ";
			$this->milesBegin = 0;
			$this->milesEnd = 0;
		}

		//Setters for fields that all Precedence Types contain
		public function setRpm($rpm) {
			$this->entryRpm = $rpm;
		}

		public function setFlat($flat) {
			$this->entryFlat = $flat;
		}

		public function setMin($min) {
			$this->entryMin = $min;
		}

		public function setFuel($fuel) {
			$this->entryFuel = $fuel;
		}

		public function setComment($comment) {
			$this->entryComments = $comment;
		}
	}

	//Interface enforces consistency for all Entries to use setters for their Destinations and Origins
	interface EntryInt {
		public function setOrgA($orgA);
		public function setOrgB($orgB);
		public function setDesA($desA);
		public function setDesB($desB);
	}

	//Class Definition for Entry Precedence One (Zip6 to Zip6)
	class EntryPREC5 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgZip6A = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip6B = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip6A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip6B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgZip6A;
			$fieldArray[1] = $this->orgZip6B;
			$fieldArray[2] = $this->desZip6A;
			$fieldArray[3] = $this->desZip6B;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Two (City, State to City, State)
	class EntryPREC10 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgCity = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desCity = $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgCity;
			$fieldArray[1] = $this->orgState;
			$fieldArray[2] = $this->desCity;
			$fieldArray[3] = $this->desState;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Three (Zip5 to Zip3)
	class EntryPREC15 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgZip5A = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip5B = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip3A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip3B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgZip5A;
			$fieldArray[1] = $this->orgZip5B;
			$fieldArray[2] = $this->desZip3A;
			$fieldArray[3] = $this->desZip3B;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Four (Zip3 to Zip5)
	class EntryPREC20 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgZip3A = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip3B = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip5A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip5B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgZip3A;
			$fieldArray[1] = $this->orgZip3B;
			$fieldArray[2] = $this->desZip5A;
			$fieldArray[3] = $this->desZip5B;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Five (Zip3 to City, State)
	class EntryPREC25 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgZip3A = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip3B = $orgB;
		}

		public function setDesA($desA) {
			$this->desCity = $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgZip3A;
			$fieldArray[1] = $this->orgZip3B;
			$fieldArray[2] = $this->desCity;
			$fieldArray[3] = $this->desState;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Six (City, State to Zip3)
	class EntryPREC30 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgCity = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip3A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip3B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgCity;
			$fieldArray[1] = $this->orgState;
			$fieldArray[2] = $this->desZip3A;
			$fieldArray[3] = $this->desZip3B;
			$fieldArray[4] = $this->entryRpm;
			$fieldArray[5] = $this->entryFlat;
			$fieldArray[6] = $this->entryMin;
			$fieldArray[7] = $this->entryFuel;
			$fieldArray[8] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Six (City, State to Zip3)
	class EntryPREC31 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip3A = $orgB;
		}

		public function setOrgC($orgC) {
			$this->orgZip3B = $orgC;
		}

		public function setDesA($desA) {
			$this->desState = $desA;
		}

		public function setDesB($desB) {
			$this->desZip3A = $desB;
		}

		public function setDesC($desC) {
			$this->desZip3B = $desC;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->orgZip3A;
			$fieldArray[2] = $this->orgZip3B;
			$fieldArray[3] = $this->desState;
			$fieldArray[4] = $this->desZip3A;
			$fieldArray[5] = $this->desZip3B;
			$fieldArray[6] = $this->entryRpm;
			$fieldArray[7] = $this->entryFlat;
			$fieldArray[8] = $this->entryMin;
			$fieldArray[9] = $this->entryFuel;
			$fieldArray[10] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Six (City, State to State)
	class EntryPREC33 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgCity = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->orgState= $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgCity;
			$fieldArray[1] = $this->orgState;
			$fieldArray[2] = $this->desState;
			$fieldArray[3] = $this->entryRpm;
			$fieldArray[4] = $this->entryFlat;
			$fieldArray[5] = $this->entryMin;
			$fieldArray[6] = $this->entryFuel;
			$fieldArray[7] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Seven (State to Zip5)
	class EntryPREC35 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip5A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip5B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->desZip5A;
			$fieldArray[2] = $this->desZip5B;
			$fieldArray[3] = $this->entryRpm;
			$fieldArray[4] = $this->entryFlat;
			$fieldArray[5] = $this->entryMin;
			$fieldArray[6] = $this->entryFuel;
			$fieldArray[7] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Six (City, State to State)
	class EntryPREC37 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desCity= $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->desCity;
			$fieldArray[2] = $this->desState;
			$fieldArray[3] = $this->entryRpm;
			$fieldArray[4] = $this->entryFlat;
			$fieldArray[5] = $this->entryMin;
			$fieldArray[6] = $this->entryFuel;
			$fieldArray[7] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Eight (State to Zip3)
	class EntryPREC40 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desZip3A = $desA;
		}

		public function setDesB($desB) {
			$this->desZip3B = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->desZip3A;
			$fieldArray[2] = $this->desZip3B;
			$fieldArray[3] = $this->entryRpm;
			$fieldArray[4] = $this->entryFlat;
			$fieldArray[5] = $this->entryMin;
			$fieldArray[6] = $this->entryFuel;
			$fieldArray[7] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Nine (Zip3 to State)
	class EntryPREC45 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgZip3A = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip3B = $orgB;
		}

		public function setDesA($desA) {
			$this->orgZip3B = $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgZip3A;
			$fieldArray[1] = $this->orgZip3B;
			$fieldArray[2] = $this->desState;
			$fieldArray[3] = $this->entryRpm;
			$fieldArray[4] = $this->entryFlat;
			$fieldArray[5] = $this->entryMin;
			$fieldArray[6] = $this->entryFuel;
			$fieldArray[7] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence Ten (State to State)
	class EntryPREC50 extends Entry implements EntryInt
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgState = $orgB;
		}

		public function setDesA($desA) {
			$this->desState = $desA;
		}

		public function setDesB($desB) {
			$this->desState = $desB;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->desState;
			$fieldArray[2] = $this->entryRpm;
			$fieldArray[3] = $this->entryFlat;
			$fieldArray[4] = $this->entryMin;
			$fieldArray[5] = $this->entryFuel;
			$fieldArray[6] = $this->entryComments;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Precedence 90 (Mileage State to State)
	class EntryPREC90 extends Entry
	{
		public function setOrgA($orgA) {
			$this->orgState = $orgA;
		}

		public function setOrgB($orgB) {
			$this->orgZip3A = $orgB;
		}

		public function setOrgC($orgC) {
			$this->orgZip3B = $orgC;
		}

		public function setDesA($desA) {
			$this->desState = $desA;
		}

		public function setDesB($desB) {
			$this->desZip3A = $desB;
		}

		public function setDesC($desC) {
			$this->desZip3B = $desC;
		}

		public function setMBegin($miles) {
			$this->milesBegin = $miles;
		}

		public function setMEnd($miles) {
			$this->milesEnd = $miles;
		}

		public function getFields() {
			$fieldArray[0] = $this->orgState;
			$fieldArray[1] = $this->orgZip3A;
			$fieldArray[2] = $this->orgZip3B;
			$fieldArray[3] = $this->desState;
			$fieldArray[4] = $this->desZip3A;
			$fieldArray[5] = $this->desZip3B;
			$fieldArray[6] = $this->entryRpm;
			$fieldArray[7] = $this->entryFlat;
			$fieldArray[8] = $this->entryFuel;
			$fieldArray[9] = $this->milesBegin;
			$fieldArray[10] = $this->milesEnd;
			$fieldArray[11] = $this->entryComments;

			return $fieldArray;
		}
	}

	/**
	*  Entry set for Fuel Table
	*/
	class EntryFuel 
	{
		protected $entryMode;
		protected $entryName;
		protected $entryFAMT;
		protected $entryTAMT;
		protected $entryFuelIndex;
		protected $entryFuelPrcnt;

		function __construct()
		{
			$this->entryMode = "";
			$this->entryName = "";
			$this->entryFAMT = 0.0;
			$this->entryTAMT = 0.0;
			$this->entryFuelIndex = 0.0;
			$this->entryFuelPrcnt = 0.0;
		}

		public function setMode($mode) {
			$this->entryMode = $mode;
		}
		public function setName($name) {
			$this->entryName = $name;
		}
		public function setFAMT($FAMT) {
			$this->entryFAMT = $FAMT;
		}
		public function setTAMT($TAMT) {
			$this->entryTAMT = $TAMT;
		}
		public function setFuelIndex($index) {
			$this->entryFuelIndex = $index;
		}
		public function setFuelPrcnt($percent) {
			$this->entryFuelPrcnt = $percent;
		}

		public function getFields() {
			$fieldArray[0] = $this->entryMode;
			$fieldArray[1] = $this->entryName;
			$fieldArray[2] = $this->entryFAMT;
			$fieldArray[3] = $this->entryTAMT;
			$fieldArray[4] = $this->entryFuelIndex;
			$fieldArray[5] = $this->entryFuelPrcnt;

			return $fieldArray;
		}
	}

	//Class Definition for Entry Factory, instantiates a new Entry as requested.
	class EntryFactory {
		public function getNewEntry($PRECEDENCE) {
			$class = 'EntryPREC' . $PRECEDENCE;
			return new $class();
		}
	}

?>