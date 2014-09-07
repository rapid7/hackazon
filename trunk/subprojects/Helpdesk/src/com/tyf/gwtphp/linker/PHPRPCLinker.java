/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * This framework is based on GWT.
 * Design, strategies and part of the methods documentation are developed by Google Inc.
 * PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.
 * Additional modifications, GWT generators and linkers by Yifei Teng. All rights reserved.
 * For more information, please see {@link https://github.com/tengyifei/gwtphp}
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
package com.tyf.gwtphp.linker;

import java.util.Set;

import com.google.gwt.core.ext.LinkerContext;
import com.google.gwt.core.ext.TreeLogger;
import com.google.gwt.core.ext.UnableToCompleteException;
import com.google.gwt.core.ext.linker.AbstractLinker;
import com.google.gwt.core.ext.linker.ArtifactSet;
import com.google.gwt.core.ext.linker.LinkerOrder;
import com.google.gwt.user.rebind.SourceWriter;
import com.tyf.gwtphp.linker.types.Field;
import com.tyf.gwtphp.linker.types.Function;
import com.tyf.gwtphp.linker.types.ObjectArtifact;
import com.tyf.gwtphp.linker.types.ServiceArtifact;

@LinkerOrder(LinkerOrder.Order.PRE)
public class PHPRPCLinker extends AbstractLinker {

	private static final String GWTPHP_CLASS_SUFFIX = ".class.php";
	private static final String GWTPHP_HEADER_SUFFIX = ".gwtphpmap.inc.php";

	@Override
	public String getDescription() {
		return "[Generate PHP headers for RPC]";
	}

	public ArtifactSet link(TreeLogger logger, LinkerContext context, ArtifactSet artifacts)
			throws UnableToCompleteException {
		
		ArtifactSet toReturn = new ArtifactSet(artifacts);
		logger.log(TreeLogger.INFO, "Generating PHP header files...");
		
		for (ServiceArtifact service : artifacts.find(ServiceArtifact.class)) {
			emitService(logger, toReturn, service);
		}
		for (ObjectArtifact object : artifacts.find(ObjectArtifact.class)) {
			emitObject(logger, toReturn, object);
		}

		return toReturn;
	}

	private void emitObject(TreeLogger logger, ArtifactSet toReturn, ObjectArtifact object)
			throws UnableToCompleteException {
		logger.log(TreeLogger.INFO, "Processing "+object.getClassName());

		toReturn.add(emitString(logger, writeObjectHeader(object), 
				"gwtphp-maps/"+object.getClassDirName() + GWTPHP_HEADER_SUFFIX));
		
		toReturn.add(emitString(logger, writeObjectClass(object), 
				"gwtphp-maps/"+object.getClassDirName() + GWTPHP_CLASS_SUFFIX));
	}

	private String writeObjectClass(ObjectArtifact object) {
		CustomIndentSourceWriter src = new CustomIndentSourceWriter("\t");
		
		src.println("<?php");
		src.print("class %s ", object.getSimpleClassName());
		if (object.getSimpleParentClassName()!=null)
			src.print("extends %s ", object.getSimpleParentClassName());
		src.println("implements IsSerializable {");
		src.indent();
		
		Set<String> keys = object.getFields().keySet();
		for (String key : keys){
			Field f = object.getFields().get(key);
			// generate PHP documentation
			src.beginJavaDocComment();
			src.println();
			src.println("@var "+f.getTypePHP());
			src.endJavaDocComment();
			
			//write field body
			//TODO: handle different accessibility
			src.println("public $%s;", f.getName());
			src.println();
		}
		
		src.outdent();
		src.println("}");		//end class definition
		
		return src.toString();
	}

	private String writeObjectHeader(ObjectArtifact object) {
		CustomIndentSourceWriter src = new CustomIndentSourceWriter("\t");
		Set<String> keys = object.getFields().keySet();
		
		src.println("<?php");
		src.println("$gwtphpmap = array(");
		src.indent();
		src.println("'className' => '%s',", object.getClassName());
		src.println("'mappedBy' => '%s',", object.getClassName());
		src.println("'typeCRC' => '%s',", object.getTypeCRC());
		if (object.isInterface()) src.println("'isInterface' => 'true',");
		if (object.isAbstract()) src.println("'isAbstract' => 'true',");
		
		src.println("'fields' => array (");
		src.indent();
		int counter = 0;
		for (String key : keys){
			src.println("array(");
			src.indent();
			Field f = object.getFields().get(key);
			src.println("'name' => '%s',", f.getName());
			src.println("'type' => '%s',", f.getType());
			src.outdent();
			if (++counter!=keys.size())		//end of individual field element
				src.println("),");
			else
				src.println(")");
		}
		src.outdent();
		src.println("),");		//end of fields array
		if (object.getParentClassName()!=null){
			src.println("'extends' => '%s'", object.getParentClassName());
		}
		src.outdent();
		src.println(");");		//end of object definition
		
		return src.toString();
	}

	private void emitService(TreeLogger logger, ArtifactSet toReturn, ServiceArtifact service)
			throws UnableToCompleteException {
		logger.log(TreeLogger.INFO, "Processing "+service.getClassName());
		
		toReturn.add(emitString(logger, writeServiceHeader(service),
				"gwtphp-maps/"+service.getClassDirName() + GWTPHP_HEADER_SUFFIX));
		
		toReturn.add(emitString(logger, writeServiceClass(service), 
				"gwtphp-maps/"+service.getClassDirName() + GWTPHP_CLASS_SUFFIX));
	}

	private String writeServiceClass(ServiceArtifact service) {
		Set<String> keys = service.getMethods().keySet();
		CustomIndentSourceWriter src = new CustomIndentSourceWriter("\t");
		
		src.println("<?php");
		src.println("abstract class %s implements RemoteService {", service.getSimpleClassName());
		src.indent();
		for (String key : keys){
			Function f = service.getMethods().get(key);
			src.println();
			src.print("public abstract function %s(", f.getName());
			//write argument list
			for (int i=0; i<f.getParamNames().length; i++){
				if (i!=0) src.print(", ");
				src.print("$"+f.getParamNames()[i]);
			}
			src.println(");");		//closes abstract function definition
		}
		src.outdent();
		src.println("}");		//closes class definition
		return src.toString();
	}

	/**
	 * Generates the <class name>.gwtphpmap.inc.php file contents
	 * 
	 * @param service
	 * @return
	 */
	private String writeServiceHeader(ServiceArtifact service) {
		Set<String> keys = service.getMethods().keySet();
		CustomIndentSourceWriter src = new CustomIndentSourceWriter("\t");
		
		src.println("<?php");
		src.println("if (!isset($gwtphpmap)) $gwtphpmap = array();");
		src.println("$gwtphpmap[] = ");
		src.indent();
		src.println("array(");
		src.println("'className' => '%s',", service.getClassName());
		src.println("'mappedBy' => '%s',", service.getClassName());
		src.println("'methods' => array (");
		src.indent();
		int counter = 0;
		for (String key : keys){
			src.println("array(");
			src.indent();
			Function f = service.getMethods().get(key);
			src.println("'name' => '%s',", f.getName());
			src.println("'mappedName' => '%s',", f.getName());
			src.println("'returnType' => '%s',", f.getReturnType());
			src.println("'returnTypeCRC' => '%s',", f.getReturnTypeCRC());
			src.println("'params' => array(");
			writeArray(src, f.getParamsTypeNames());
			src.println(") ,");					//end of params array
			src.println("'throws' => array(");
			writeArray(src, f.getExceptions());
			src.println(")");					//end of throws array
			src.outdent();
			if (++counter!=keys.size())		//end of individual method element
				src.println("),");
			else
				src.println(")");
		}
		src.outdent();
		src.println("),");		//end of methods array
		if (service.getParentClassName()!=null){
			src.println("'extends' => '%s'", service.getParentClassName());
		}
		src.outdent();
		src.println(");");		//end of gwtphpmap element
		
		return src.toString();
	}

	private void writeArray(SourceWriter src, String[] params) {
		src.indent();
		for (String p : params){
			src.println("array('type' => '%s'),", p);
		}
		src.outdent();
	}

}
