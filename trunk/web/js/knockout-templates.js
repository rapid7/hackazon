        <script type="text/html" id="task-template">
            <li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-d ui-li-has-count">Friday, October 8, 2010 <span class="ui-li-count ui-btn-up-c ui-btn-corner-all" data-bind="text: Task.price"></span></li>
            <li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="d" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d">
                <div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="#taskDetailPage" data-bind="attr: { onclick: 'sessionStorage.TaskId=' + Task.id }"  class="ui-link-inherit"><p class="ui-li-aside ui-li-desc"><strong>6:24</strong>PM</p>
                            <h3 class="ui-li-heading" data-bind="text: Task.name"></h3>
                            <p class="ui-li-desc" data-bind="text: Task.description"><strong ></strong></p>
                        </a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
            </li>
        </script>

        
        <script type="text/html" id="task-detail-template">
            <h2 data-bind="text: Task.name"></h2>
            <div data-bind="template: { name: Task.ControlTemplate, foreach: Task.controls, as: 'control' }">
            </div>
        </script>      
        
        <script type="text/html" id="message">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="singleanswer" data-theme="b" >
                <fieldset data-role="controlgroup">
                    <legend data-bind="text: control.name"></legend>
                    <div data-bind="foreach: {data: control.property }" class="ui-controlgroup-controls" >
                        <div data-bind="text: value"/>
                    </div>
                </fieldset>
            </div>
        </script>           
        
        <script type="text/html" id="singleanswer">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="singleanswer" data-theme="b" >
                <fieldset data-role="controlgroup">
                    <legend data-bind="text: control.name"></legend>
                    <div data-bind="foreach: {data: control.property }" class="ui-controlgroup-controls" >
                        <!-- ko if: name != 'allowuserown' -->
                            <input type="radio" data-bind="attr: { 'name': 'sa_' + control.control.id, 'id': 'sa_' + id, 'value': id }" />
                            <label data-bind="attr: { 'for': 'sa_' + id }, 'text': value"/>
                        <!-- /ko -->                    
                        <!-- ko if: name == 'allowuserown' -->
                            <label class="ui-hidden-accessible" data-bind="attr: { 'for': 'sa_' + id }, 'text': 'Type your answer'" style="padding-top:5px"/>
                            <input type="text" data-bind="attr: { 'name': 'sa_' + control.control.id, 'id': 'sa_' + id }" placeholder="Type your answer" />
                        <!-- /ko -->                    
                
                    </div>
                </fieldset>
            </div>
        </script>   
        
        <script type="text/html" id="multianswer">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="multianswer" data-theme="b" >
                <fieldset data-role="controlgroup">
                    <legend data-bind="text: control.name"></legend>
                    <div data-bind="foreach: {data: control.property }" class="ui-controlgroup-controls" >
                        <!-- ko if: name != 'allowuserown' -->
                            <input type="checkbox" data-bind="attr: { 'name': 'ma_' + control.control.id, 'id': 'ma_' + id, 'value': id }" />
                            <label data-bind="attr: { 'for': 'ma_' + id}, 'text': value"/>
                        <!-- /ko -->                    
                        <!-- ko if: name == 'allowuserown' -->
                            <label class="ui-hidden-accessible" data-bind="attr: { 'for': 'ma_' + id}, 'text': 'Type your answer'" style="padding-top:5px"/>
                            <input type="text" data-bind="attr: { 'name': 'ma_' + control.control.id, 'id': 'ma_' + id }" placeholder="Type your answer"/>
                        <!-- /ko -->
                    </div>
                </fieldset>
            </div>
        </script>        
        
        <script type="text/html" id="truefalse">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="truefalse" data-theme="b" >
                <fieldset data-role="controlgroup">
                    <legend data-bind="text: control.name"></legend>
                    <div data-bind="foreach: {data: control.property }" class="ui-controlgroup-controls" >
                        <!-- ko if: name != 'allowuserown' -->
                            <input type="radio" data-bind="attr: { 'name': 'tf_' + control.control.id, 'id': 'tf_' + id, 'value': id }" />
                            <label data-bind="attr: { 'for': 'tf_' + id}, 'text': value"/>
                        <!-- /ko -->                         
                    </div>
                </fieldset>
            </div>
        </script>          
        
        <script type="text/html" id="photo">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="photo">
                <!-- ko foreach: new Array( parseInt(control.property[0].value) )  -->
                    <fieldset data-role="controlgroup" >
                        <legend data-bind="text: control.control.name  + '   ' + ($index()+1)"></legend>
                        <button data-role="capture" data-bind="attr: { 'data-id': 'p' + control.control.property[0].id + $index()  }">Capture Photo</button>
                        <button data-role="gallery" data-bind="attr: { 'data-id': 'p' + control.control.property[0].id + $index()  }">Photo Gallery</button>
                        <img style="width:50px;height:50px;display:none" src="" data-bind="attr: { 'name': 'p' + control.control.property[0].id, 'id': 'p' + control.control.property[0].id + $index() }" />
                    </fieldset>            
                <!-- /ko -->​
            </div>
        </script>   

        <script type="text/html" id="video">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="video">
                <!-- ko foreach: new Array( parseInt(control.property[0].value) )  -->
                    <fieldset data-role="controlgroup">
                        <legend data-bind="text: control.control.name  + '   ' + ($index()+1)"></legend>
                        <button data-role="capture" data-bind="attr: { 'data-id': 'v' + control.control.property[0].id + $index()  }">Capture Video</button>
                        <button data-role="gallery" data-bind="attr: { 'data-id': 'v' + control.control.property[0].id + $index()  }">Video Gallery</button>
                        <video data-bind="attr: { 'id': 'v' + + control.control.property[0].id }" controls style="height:100px; width:100px">
                            <source src="" type="video/mp4" />
                        </video>
                    </fieldset>
                <!-- /ko -->​
            </div>
        </script>           

        <script type="text/html" id="audio">
            <div data-role="fieldcontain" class="ui-corner-all" data-controltype="audio">
                <!-- ko foreach: new Array( parseInt(control.property[0].value) )  -->
                    <fieldset data-role="controlgroup">
                        <legend data-bind="text: control.control.name  + '   ' + ($index()+1)"></legend>
                        <button data-role="capture" data-bind="attr: { 'data-id': 'a' + control.control.property[0].id + $index()  }">Capture Audio</button>
                        <button data-role="gallery" data-bind="attr: { 'data-id': 'a' + control.control.property[0].id + $index()  }">Audio Gallery</button>
                        <audio  data-bind="attr: { 'id': 'a' + + control.control.property[0].id }" controls style="height:50px; width:100%">
                            <source src="" type="audio/mpeg" />
                        </audio>
                    </fieldset>    
                <!-- /ko -->​
            </div>
        </script>          