<div id="middle" style=" height:310px; min-height: 310px;">
    <div class="content_container" style="width:100%" >
        <div align="center">
    	    <h2 style="">Signature</h2>
            <canvas id="myCanvas" width="476" height="200" style="border:2px solid black; background:#FFFFFF; cursor:url('/images/pen.png');"></canvas>
            <br /><br />
            <button onclick="javascript:save(<?php echo $_REQUEST['sign']; ?>);return false;">Save</button>
            <button onclick="javascript:clearArea();return false;">Clear Area</button>
            <div style="display:none;">
            Line width : <select id="selWidth">
                <option value="1">1</option>
                <option value="3" selected="selected">3</option>
                <option value="5">5</option>
                <option value="7">7</option>
                <option value="9">9</option>
                <option value="11">11</option>
            </select> 
            Color : <select id="selColor">
                <option value="black" selected="selected">black</option>
                <option value="blue">blue</option>
                <option value="red">red</option>
                <option value="green">green</option>
                <option value="yellow">yellow</option>
                <option value="gray">gray</option>
            </select> 
            </div>
        </div>
    </div>
</div>
<div style="clear:both;"></div>