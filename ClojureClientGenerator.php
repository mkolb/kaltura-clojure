<?php
class ClojureClientGenerator extends ClientGeneratorFromXml
{
	private $schemaXml;
	
	/**
	* Constructor.
	* @param string $xmlPath path to schema xml
	* @link http://www.kaltura.com/api_v3/api_schema.php
	*/
	function ClojureClientGenerator($xmlPath)
	{
		parent::ClientGeneratorFromXml($xmlPath, realpath("sources/clojure"));
	}
	
	/**
	* Parses the higher-level of the schema, divide parsing to five steps:
	* Enum creation, Object (VO) classes, Services and actions, Main, and project file.
	*/
	public function generate() 
	{	
		$this->schemaXml = new SimpleXMLElement( $this->_xmlFile , NULL, TRUE);
		
		//parse object types
		foreach ($this->schemaXml->children() as $reflectionType) 
		{
			switch($reflectionType->getName())
			{
				case "enums":
					//create enum classes
					foreach($reflectionType->children() as $enums_node)
					{
						$this->writeEnum($enums_node);
					}
				break;
				case "classes":
					//create object classes
					foreach($reflectionType->children() as $classes_node)
					{
						$this->writeObjectClass($classes_node);
					}
				break;
				case "services":
					//implement services (api actions)
					foreach($reflectionType->children() as $services_node)
					{
						$this->writeService($services_node);
					}
					//write main class (if needed, this can also be included in the static sources folder if not dynamic)
					$this->writeMainClass($reflectionType->children());
				break;	
			}
		}
		//write project file (if needed, this can also be included in the static sources folder if not dynamic)
		$this->writeProjectFile();
	}
	
	/**
	* Parses Enum (aka. types) classes.
	*/
	protected function writeEnum(SimpleXMLElement $enumNode)
	{
		//override to implement the parsing and file creation.
		
		//to get the name of the class, use the name attribute
		echo 'Create ENUM: '.$enumNode->attributes()->name."\r\n";
		//parse the class constants
		foreach($enumNode->children() as $child) {
			echo "\tconst: " . $child->attributes()->name . " : int = " . $child->attributes()->value . ";\r\n"; 
		}
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
	}
	
	/**
	* Parses Object (aka. VO) classes.
	*/
	protected function writeObjectClass(SimpleXMLElement $classNode)
	{
		//override to implement the parsing and file creation.
		
		//to get the name of the class, use the name attribute
		echo 'Create Class: '.$classNode->attributes()->name."\r\n";
		//parse the class base class (parent in heritage)
		if($classNode->attributes()->base)
			echo "\textends: " . $classNode->attributes()->base;
		//parse the class properties
		foreach($classNode->children() as $classProperty) {
			echo "\t\tproperty: " . $classProperty->attributes()->name . " : " . $classProperty->attributes()->type . ";\r\n"; 
		}
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
	}
	
	/**
	* Parses Services and actions (calls that can be performed on the objects).
	*/
	protected function writeService(SimpleXMLElement $serviceNodes)
	{
		//override to implement the parsing and file creation.
		
		//to get the name of the class, use the name attribute
		echo 'Create Service: '.$serviceNodes->attributes()->name."\r\n";
		//parse the service actions
		foreach($serviceNodes->children() as $action) {
			echo "\tAction: " . $action->attributes()->name . "\r\n";
			//parse the actions parameters and result types
			foreach($action->children() as $prop) {
				if($prop->getName() == "param" ) {
					//action paramer:
					echo "\t\tParameter: " . $prop->attributes()->name.' : '.$prop->attributes()->type;
					if($prop->attributes()->optional == "1") {
						if($prop->attributes()->default && $prop->attributes()->default != "")
							echo " = " . $prop->attributes()->default;
					}
					echo "\r\n\t\t\tDescription: ".$prop->attributes()->description."\r\n";
				} else {
					//action result type:
					echo "\t\t".$prop->getName().":".$prop->attributes()->type."\r\n";
				}
			}
		}
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
	}
	
	/**
	* Create the main class of the client library, may parse Services and actions.
	*/
	protected function writeMainClass(SimpleXMLElement $servicesNodes)
	{
		//override to implement the parsing and file creation.
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
		echo "Create Main File.\r\n";
	}
	
	/**
	* Create the project file (when needed).
	*/
	protected function writeProjectFile()
	{
		//override to implement the parsing and file creation.
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
		echo "Create Project File.\r\n";
	}
}
?>