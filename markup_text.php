<div id="mainContainer">
<style>
	table{width:100%;color:#000}table tr td{padding:10px 5px}textarea.input_small{height:100px}span.locked{display:inline-flex;height:20px;line-height:20px;width:100%}
</style>
<fieldset class="roundCorner">
    <legend style="color:#000000;">Text</legend>
    <form id="addhotspotFrm" name="addhotspotFrm" action="">
        <table width="100%" border="0">
            <tbody>
                <tr>
                    <td style="color:#000000;width:150px"> Text </td>
                    <td><textarea id="title" name="title" cols="30" rows="3" style="color:#ff0000;"><?php echo (isset($hotspotData['title']) && !empty($hotspotData['title']))?$hotspotData['title']:''; ?></textarea>
                    </td>
                    
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="left">
                        <input type="button" id="saveHotspotId" name="save_hotspot" class="form_btn" value="Save" onclick="saveHotspot('<?=$_GET['X']?>','<?=$_GET['Y']?>');" class="hotspot-button">
                    </td>
                    <td>&nbsp;</td>
                    <input type="hidden" name="operationTag" id="operationTag" value="<?=$operationTag?>">
                    <input type="hidden" name="hotspotID" id="hotspotID" value="<?=$hotspotID?>">
                    <input type="hidden" name="tagWidth" id="tagWidth" value="<?=$tagWidth?>">
                    <input type="hidden" name="tagHeight" id="tagHeight" value="<?=$tagHeight?>">
                    <input type="hidden" name="tagPosLeft" id="tagPosLeft" value="<?=$tagPosLeft?>">
                    <input type="hidden" name="tagPosTop" id="tagPosTop" value="<?=$tagPosTop?>">
                    <input type="hidden" name="tagshape" id="tagshape" value="<?=$tagshape?>">
                    <input type="hidden" name="degree" id="degree" value="<?=$degree?>">
                    <input type="hidden" name="j_data" id="j_data" value="<?=$j_data?>">
                    <input type="hidden" name="hotspot_move" id="hotspot_move" value="false">
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>

</div>