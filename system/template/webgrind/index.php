<?php 
	$_->incl('header','webgrind');
?>
	<title>webgrind</title>
	<script type="text/javascript" charset="utf-8">
		var fileUrlFormat = '<?php echo \Core\Debug\WebGrind\Config::$fileUrlFormat?>';
		var currentDataFile = null;
		var callInfoLoaded = new Array();
		var disableAjaxBlock = false;
		function getOptions(specificFile){
			var options = new Object();
			options.dataFile = specificFile || $("#dataFile").val();
			options.costFormat = $('#costFormat').val();
			options.showFraction = $("#showFraction").val();
			options.hideInternals = $('#hideInternals').attr('checked') ? 1 : 0;
			options.debug = 'webgrind';
			return options;
		}
		
		function update(specificFile){
			vars = getOptions(specificFile);
			vars.op = 'function_list';
			$.getJSON("index.php",
				vars,
				function(data){
					callInfoLoaded = new Array();
					$("#function_table tbody").empty();
					for(i=0;i<data.functions.length;i++){
						callInfoLoaded[data.functions[i].nr] = false;
						$("#function_table tbody").append(functionTableRow(data.functions[i]));
					}
					currentDataFile = data.dataFile;
					$("#data_file").html(data.dataFile);
					$("#invoke_url").html(data.invokeUrl);
					$(document).attr('title', 'webgrind of '+data.invokeUrl);
					$("#mtime").html(data.mtime);
					$("#shown_sum").html(data.functions.length);
					$("#invocation_sum").html(data.summedInvocationCount);
					$("#runtime_sum").html(data.summedRunTime);
					$("#runs").html(data.runs);
					
					var breakdown_sum = data.breakdown['internal']+data.breakdown['user']+data.breakdown['class']+data.breakdown['include'];
                    $("#breakdown").html(
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_left.png')?>" height="20" width="10">'+
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_blue.png')?>" height="20" width="'+Math.floor(data.breakdown['internal']/breakdown_sum*300)+'">'+
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_grey.png')?>" height="20" width="'+Math.floor(data.breakdown['include']/breakdown_sum*300)+'">'+
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_green.png')?>" height="20" width="'+Math.floor(data.breakdown['class']/breakdown_sum*300)+'">'+
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_orange.png')?>" height="20" width="'+Math.floor(data.breakdown['user']/breakdown_sum*300)+'">'+
                        '<img src="<?=$_->u('/system/images/webgrind/gradient_right.png')?>" height="20" width="10">'+
                        '<div title="internal functions, include/require, class methods and procedural functions." style="background:url(/system/images/webgrind/gradient_markers.png);position:relative;top:-20px;left:10px;width:301px;height:19px"></div>'
                    );
					
					$("#hello_message").hide();
					$("#trace_view").show();
					
					$("#function_table").trigger('update');
					$("#function_table").trigger("sorton",[[[4,1]]]); 
				}
			);
		}
		
		function loadCallInfo(functionNr){			
			$.getJSON("index.php",
				{'debug':'webgrind', 'op':'callinfo_list', 'file':currentDataFile, 'functionNr':functionNr, 'costFormat':$("#costFormat").val()},
				function(data){
					
					if(data.calledByHost)
						$("#callinfo_area_"+functionNr).append('<b>Called from script host</b>');
					
					insertCallInfo(functionNr, 'sub_calls_table_', 'Calls', data.subCalls);
					insertCallInfo(functionNr, 'called_from_table_', 'Called From', data.calledFrom);

					
					callInfoLoaded[functionNr] = true;
				}
			);				
		
		}
		
		function insertCallInfo(functionNr, idPrefix, title, data){
			if(data.length==0)
				return;
				
			$("#callinfo_area_"+functionNr).append(callTable(functionNr,idPrefix, title));
			
			for(i=0;i<data.length;i++){
				$("#"+idPrefix+functionNr+" tbody").append(callTableRow(i, data[i]));
			}
			
			$("#"+idPrefix+functionNr).tablesorter({
				widgets: ['zebra'],
				headers: { 
		            3: { 
		                sorter: false 
		            }
		        }							
			});
			$("#"+idPrefix+functionNr).bind("sortStart",sortBlock).bind("sortEnd",$.unblockUI);
			$("#"+idPrefix+functionNr).trigger("sorton",[[[2,1]]]); 				
				
			
		}
		
		function callTable(functionNr, idPrefix, title){
			return '<table class="tablesorter" id="'+idPrefix+functionNr+'" cellspacing="0"> \
						<thead><tr><th><span>'+title+'</span></th><th><span>Count</span></th><th><span>Total Call Cost</span></th><th> </th></tr></thead> \
						<tbody> \
						</tbody> \
					</table> \
			';
		}
		
		function callTableRow(nr,data){
			return '<tr> \
						<td>'
						+($("#callinfo_area_"+data.functionNr).length ? '<img src="/system/images/webgrind/right.gif">&nbsp;&nbsp;<a href="javascript:openCallInfo('+data.functionNr+')">'+data.callerFunctionName+'</a>' : '<img src="/system/images/webgrind/blank.gif">&nbsp;&nbsp;'+data.callerFunctionName)
						+ ' @ '+data.line+'</td> \
						<td class="nr">'+data.callCount+'</td> \
						<td class="nr">'+data.summedCallCost+'</td> \
						<td><a title="Open file and show line" href="'+sprintf(fileUrlFormat,data.file,data.line)+'" target="_blank"><img src="/system/images/webgrind/file_line.png" alt="O"></a></td> \
					</tr>';
			
		}
				
		function toggleCallInfo(functionNr){
			if(!callInfoLoaded[functionNr]){
				loadCallInfo(functionNr);
			}					
			
			$("#callinfo_area_"+functionNr).toggle();
			current = $("#fold_marker_"+functionNr).get(0).src;
			if(current.substr(current.lastIndexOf('/')+1) == 'right.gif')
				$("#fold_marker_"+functionNr).get(0).src = '/system/images/webgrind/down.gif';
			else
				$("#fold_marker_"+functionNr).get(0).src = '/system/images/webgrind/right.gif';
		}
		
		function openCallInfo(functionNr) {
			var areaEl = $("#callinfo_area_"+functionNr);
			if (areaEl.length) {
    			if (areaEl.is(":hidden")) toggleCallInfo(functionNr);
    			window.scrollTo(0, areaEl.parent().offset().top);
    		}
		}
		
		function functionTableRow(data){
			openLink = (data.file=='php:internal')?'':'<a title="Open file" href="'+sprintf(fileUrlFormat,data.file,-1)+'" target="_blank"><img src="/system/images/webgrind/file.png" alt="O"></a>';
			return '<tr> \
			            <td> \
			                <img src="/system/images/webgrind/call_'+data.kind+'.png" title="'+data.humanKind+'"> \
			            </td> \
						<td> \
							<a href="javascript:toggleCallInfo('+data.nr+')"> \
								<img id="fold_marker_'+data.nr+'" src="/system/images/webgrind/right.gif">&nbsp;&nbsp;'+data.functionName+' \
							</a> \
							<div class="callinfo_area" id="callinfo_area_'+data.nr+'"></div> \
						</td> \
						<td>'+openLink+'</td> \
						<td class="nr">'+data.invocationCount+'</td> \
						<td class="nr">'+data.summedSelfCost+'</td> \
						<td class="nr">'+data.summedInclusiveCost+'</td> \
					</tr> \
			';
		}
		
		function sortBlock(){
			$.blockUI('<div class="block_box"><h1>Sorting...</h1></div>');
		}
		
		function loadBlock(){
			if(!disableAjaxBlock)
				$.blockUI();
			disableAjaxBlock = false;
		}
		
		$(document).ready(function() { 
			
			$.blockUI.defaults.pageMessage = '<div class="block_box"><h1>Loading...</h1><p>Loading information from server. If the callgrind file is large this may take some time.</p></div>';
			$.blockUI.defaults.overlayCSS =  { backgroundColor: '#fff', opacity: '0' }; 
			$.blockUI.defaults.fadeIn = 0;
			$.blockUI.defaults.fadeOut = 0;
			$().ajaxStart(loadBlock).ajaxStop($.unblockUI);
			$("#function_table").tablesorter({
				widgets: ['zebra'],
				sortInitialOrder: 'desc',
				headers: { 
		            1: { 
		                sorter: false 
		            },
		            2: {
		                sorter: false
		            }
		        }
			});
			$("#function_table").bind("sortStart",sortBlock).bind("sortEnd",$.unblockUI);
			
			update();
		});
		
	</script>
</head>
<body>
    <div id="head">
        <div id="logo">
            <h1>webgrind<sup style="font-size:10px">v<?php echo \Core\Debug\WebGrind\Config::$webgrindVersion?></sup></h1>
            <p>profiling in the browser</p>
        </div>
        <div id="options">
            <form method="get" onsubmit="update();return false;">
            	<div style="float:right;margin-left:10px">
            	    <input type="submit" value="update">
            	</div>
            	<div style="float:right;">
            		<label style="margin:0 5px">in</label>
            		<select id="costFormat" name="costFormat">
            		    <option value="percent" <?php echo (\Core\Debug\WebGrind\Config::$defaultCostformat=='percent') ? 'selected' : ''?>>percent</option>
            		    <option value="msec" <?php echo (\Core\Debug\WebGrind\Config::$defaultCostformat=='msec') ? 'selected' : ''?>>milliseconds</option>
            		    <option value="usec" <?php echo (\Core\Debug\WebGrind\Config::$defaultCostformat=='usec') ? 'selected' : ''?>>microseconds</option>
            		</select>
            	</div>
            	<div style="float:right;">
            		<label style="margin:0 5px">of</label>
            		<input id="dataFile" name="dataFile" style="width:200px" value="<?=$_->vars['filename']?>" disabled="disabled">
            	</div>
            	<div style="float:right">
            		<label style="margin:0 5px">Show</label>
            		<select id="showFraction" name="showFraction">
            			<?php for($i=100; $i>0; $i-=10):?>
            			<option value="<?php echo $i/100?>" <?php if ($i==\Core\Debug\WebGrind\Config::$defaultFunctionPercentage):?>selected="selected"<?php endif;?>><?php echo $i?>%</option>
            			<?php endfor;?>
            		</select>
            	</div>
            	<div style="clear:both;"></div>    	
            	<div style="margin:0 70px">
            	    <input type="checkbox" name="hideInternals" value="1" <?php echo (\Core\Debug\WebGrind\Config::$defaultHideInternalFunctions==1) ? 'checked' : ''?> id="hideInternals">
            	    <label for="hideInternals">Hide PHP functions</label>
            	</div>
            </form>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div id="main">

        <div id="trace_view">
        	<div style="float:left;">
        	    <h2 id="invoke_url"></h2>
        	    <span id="data_file"></span> @ <span id="mtime"></span>
        	</div>
        	<div style="float:right;">
        	    <div id="breakdown" style="margin-bottom:5px;width:320px;height:20px"></div>
        	    <span id="invocation_sum"></span> different functions called in <span id="runtime_sum"></span> milliseconds (<span id="runs"></span> runs, <span id="shown_sum"></span> shown)
        	</div>
        	<div style="clear:both"></div>
        	<table class="tablesorter" id="function_table">
        		<thead>
        		    <tr>
        		        <th> </th>
        		        <th><span>Function</span></th>
        		        <th> </th>
        		        <th><span>Invocation Count</span></th>
        		        <th><span>Total Self Cost</span></th>
        		        <th><span>Total Inclusive Cost</span></th>
        		    </tr>
        		</thead>
        		<tbody>
        		</tbody>
        	</table>
        </div>
        <h2 id="hello_message">Select a cachegrind file above</h2>
        <div id="footer">
            Copyright © 2008 Jacob Oettinger &amp; Joakim Nygård. <a href="http://code.google.com/p/webgrind/">webgrind homepage</a>
        </div>
    </div>
</body>
</html>