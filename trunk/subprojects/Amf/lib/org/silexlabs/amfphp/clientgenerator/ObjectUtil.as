package org.silexlabs.amfphp.clientgenerator
{
	public class ObjectUtil
	{

		
		static public function deepObjectToString(obj:*, level:int = 0, output:String = ""):*  
		{  
			var tabs:String = "";  
			for( var i : int = 0 ; i < level ; i++)
				tabs += "\t";
			
			
			for (var child:* in obj)  
			{  
				output += tabs + "["+ child + "] => " + obj[child];  
				
				var childOutput:String = deepObjectToString(obj[child], level + 1);  
				if (childOutput != "") output += " {\n"+ childOutput + tabs + "}";  
				
				output += "\n";  
			}  
			
			if (level > 20) return "";  
			return output;  
		}  
	}
}