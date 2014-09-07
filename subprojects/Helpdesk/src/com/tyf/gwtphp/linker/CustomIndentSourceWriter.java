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

import java.io.PrintWriter;
import java.io.StringWriter;

import com.google.gwt.core.ext.TreeLogger;
import com.google.gwt.user.rebind.SourceWriter;

public class CustomIndentSourceWriter implements SourceWriter {
	private final String indentString;
	private final StringWriter buffer = new StringWriter();
	private int indentLevel = 0;
	private String indentPrefix = "";
	private boolean needsIndent;
	private final PrintWriter out = new PrintWriter(buffer);
	
	public CustomIndentSourceWriter(){
		this.indentString = "  ";
	}
	
	public CustomIndentSourceWriter(String indentString){
		this.indentString = indentString;
	}

	public void beginJavaDocComment() {
		println("/**");
		indentPrefix = " * ";
	}

	/**
	 * This is a no-op.
	 */
	public void commit(TreeLogger logger) {
		out.flush();
	}

	public void endJavaDocComment() {
		indentPrefix = "";
		println("*/");
	}

	public void indent() {
		indentLevel++;
	}

	public void indentln(String s) {
		indent();
		println(s);
		outdent();
	}

	public void indentln(String s, Object... args) {
		indentln(String.format(s, args));
	}

	public void outdent() {
		indentLevel = Math.max(indentLevel - 1, 0);
	}

	public void print(String s) {
		maybeIndent();
		out.print(s);
	}

	public void print(String s, Object... args) {
		print(String.format(s, args));
	}

	public void println() {
		maybeIndent();
		// Unix-style line endings for consistent behavior across platforms.
		out.print('\n');
		needsIndent = true;
	}

	public void println(String s) {
		print(s);
		println();
	}

	public void println(String s, Object... args) {
		println(String.format(s, args));
	}

	@Override
	public String toString() {
		out.flush();
		return buffer.getBuffer().toString();
	}

	private void maybeIndent() {
		if (needsIndent) {
			needsIndent = false;
			for (int i = 0; i < indentLevel; i++) {
				out.print(indentString);
				out.print(indentPrefix);
			}
		}
	}
}
