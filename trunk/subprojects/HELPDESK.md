1. Install GWT Plugin in Eclipse
    https://dl.google.com/eclipse/plugin/4.3

2. Then: File -> Import -> General -> Existing Project

3. Select Helpdesk root directory in hackazon project "subprojects\Helpdesk"

4 Click Finish.

5. Install GWT (for example in dir C:\gwt-2.6.1)

6. Go to: Window -> Preferences -> Google -> Web Toolkit

7. Click "Add" -> Enter path [C:\gwt-2.6.1] -> OK

8. Set checkbox enabled in front of added SDK

9. Click OK

10. Install firefox 24 (possible portable), and GWT plugin (when you run the app, 
    a link will be given in a browser). Plugin can work only on Firefox <= v24.

11. Now you can run this app, modify code and test it in the browser.

12. When the app is ready, r-click the project name, select "Google -> GWT Compile"
    All necessary files for deployment will be created:
        Client files here: subprojects\Helpdesk\war\Helpdesk
        PHP map files and stub classes here: subprojects\Helpdesk\war\Helpdesk\gwtphp-maps
    
13. Just copy php to: modules\gwtphp\gwtphp-maps (do not clean folder before it, 
        because there are also reside implementation classes, such as 
        modules\gwtphp\gwtphp-maps\com\ntobjectives\hackazon\helpdesk\client\HelpdeskServiceImpl.class.php)
        
    And totally replace the content of web\helpdesk with generated client files.
    
14. Submodule [subprojects\Helpdesk\src\com\tyf\gwtphp] is from GWTPHP plugin and compiles
    server interfaces into PHP. There is nothing to do with it. 
    
15. If you add service interfaces in client part of the GWT project, it is mandatory to 
    add its implementation in php (like HelpdeskServiceImpl above).
    Implementation must be named as follows: <ServiceName> + Impl.class.php
    
16. That's it! Everything else is up to you.
    
    