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
package com.tyf.gwtphp.linker.types;

import com.google.gwt.core.ext.linker.Artifact;
import com.tyf.gwtphp.linker.PHPRPCLinker;

public abstract class Element extends Artifact<Element> {
	
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;

	protected Element(String className, String simpleClassName, String parentClassName, 
			String simpleParentClassName, Boolean isInterface, Boolean isAbstract) {
		super(PHPRPCLinker.class);
		this.className = className;
		this.classDirName = className.replace('.', '/');
		this.simpleClassName = simpleClassName;
		this.parentClassName = parentClassName;
		this.simpleParentClassName = simpleParentClassName;
		this.isInterface = isInterface;
		this.isAbstract = isAbstract;
	}

	protected final String className;
	protected final String classDirName;
	protected final String simpleClassName;
	protected final String parentClassName;
	protected final String simpleParentClassName;
	private final Boolean isInterface;
	private final Boolean isAbstract;
	
	@Override
	public int hashCode() {
		return className.hashCode();
	}
	
	public String getClassName() {
		return className;
	}
	
	public String getClassDirName(){
		return classDirName;
	}
	
	public String getSimpleClassName() {
		return simpleClassName;
	}
	
	public String getParentClassName(){
		return parentClassName;
	}
	
	public Object getSimpleParentClassName() {
		return simpleParentClassName;
	}
	
	@Override
	protected int compareToComparableArtifact(Element o) {
		return className.compareTo(o.getClassName());
	}

	@Override
	protected Class<Element> getComparableArtifactType() {
		return Element.class;
	}

	public Boolean isInterface() {
		return isInterface;
	}

	public Boolean isAbstract() {
		return isAbstract;
	}
}
