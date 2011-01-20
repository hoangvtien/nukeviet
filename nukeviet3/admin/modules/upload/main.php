<?php

/**
 * @Project NUKEVIET 3.0
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES.,JSC. All rights reserved
 * @Createdate 2-9-2010 14:43
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

//age_title = $lang_module['upload_manager'];
if ( strpos( $client_info['browser']['name'], 'Internet Explorer v6' ) !== false )
{
    nv_info_die( $global_config['site_description'], $lang_global['site_info'], "<br />" . $lang_module['upload_error_browser_ie6'] );
}
/** get config file **/
$path = ( defined( 'NV_IS_SPADMIN' ) ) ? "" : NV_UPLOADS_DIR;
$path = htmlspecialchars( trim( $nv_Request->get_string( 'path', 'get', $path ) ), ENT_QUOTES );
$currentpath = $nv_Request->isset_request( 'path', 'post' ) ? htmlspecialchars( trim( $nv_Request->get_string( 'path', 'post', $path ) ), ENT_QUOTES ) : htmlspecialchars( trim( $nv_Request->get_string( 'currentpath', 'get', $path ) ), ENT_QUOTES );
if ( empty( $currentpath ) )
{
    $currentpath = NV_UPLOADS_DIR;
}
$area = "";
$popup = $nv_Request->get_int( 'popup', 'get', 0 );
$selectedfile = '';
$uploadflag = $nv_Request->isset_request( 'confirm', 'post' );
////////////////////////////////////////////////////////////////////////////////////////
$errors = array();
if ( $uploadflag )
{
    require_once ( NV_ROOTDIR . "/includes/class/upload.class.php" );
    $upload = new upload( $admin_info['allow_files_type'], $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
    if ( is_uploaded_file( $_FILES['fileupload']['tmp_name'] ) && nv_check_allow_upload_dir( $currentpath ) )
    {
        $upload_info = $upload->save_file( $_FILES['fileupload'], NV_ROOTDIR . '/' . $currentpath, false );
        if ( ! empty( $upload_info['error'] ) )
        {
            $errors[] = $upload_info['error'];
        }
        else
        {
            $selectedfile = $upload_info['basename'];
        }
    }
    elseif ( $nv_Request->isset_request( 'imgurl', 'post' ) and nv_is_url( $nv_Request->get_string( 'imgurl', 'post' ) ) )
    {
        $urlfile = trim( $nv_Request->get_string( 'imgurl', 'post' ) );
        $upload_info = $upload->save_urlfile( $urlfile, NV_ROOTDIR . '/' . $currentpath, false );
        if ( ! empty( $upload_info['error'] ) )
        {
            $errors[] = $upload_info['error'];
        }
        else
        {
            $selectedfile = $upload_info['basename'];
        }
    }
    else
    {
        $errors[] = $lang_module['upload_file_error_invalidurl'];
    }
}
$type = htmlspecialchars( trim( $nv_Request->get_string( 'type', 'get', 'file' ) ), ENT_QUOTES );
if ( $popup )
{
    $area = htmlspecialchars( trim( $nv_Request->get_string( 'area', 'get' ) ), ENT_QUOTES );
}
/////////////////////////////////////////////////////////////////////////////////////////////
$admin_theme = "";
if ( defined( 'NV_ADMIN' ) and isset( $global_config['admin_theme'] ) and file_exists( NV_ROOTDIR . "/themes/" . $global_config['admin_theme'] . "/system/upload.tpl" ) )
{
    $tpl_path = NV_ROOTDIR . "/themes/" . $global_config['admin_theme'] . "/system";
    $admin_theme = $global_config['admin_theme'];
}
elseif ( defined( 'NV_ADMIN' ) and file_exists( NV_ROOTDIR . "/themes/admin_default/system/upload.tpl" ) )
{
    $tpl_path = NV_ROOTDIR . "/themes/admin_default/system";
    $admin_theme = "admin_default";
}

$xtpl = new XTemplate( "upload.tpl", $tpl_path );
$xtpl->assign("NV_BASE_SITEURL",NV_BASE_SITEURL);
$xtpl->assign("ADMIN_THEME",$admin_theme);
$xtpl->assign("NV_OP_VARIABLE",NV_OP_VARIABLE);
$xtpl->assign("NV_NAME_VARIABLE",NV_NAME_VARIABLE);
$xtpl->assign("module_name",$module_name);
$xtpl->assign("LANG",$lang_module);
$xtpl->assign("currentpath",$currentpath);
$xtpl->assign("path",$path);
$xtpl->assign("area",$area);
$xtpl->assign("type",$type);
$xtpl->assign("funnum",$nv_Request->get_int( 'CKEditorFuncNum', 'get', 0 ));
$xtpl->assign("selectedfile",$selectedfile);
//////////////////////////////////////////////////////
if ( ! empty( $errors ) )
{
    $xtpl->assign("error",implode( "<br>", $errors ));
	$xtpl->parse( 'main.error' );
}

if ( $admin_info['allow_create_subdirectories'] )
{
    $xtpl->parse( 'main.allow_create_subdirectories' );
}
if ( $admin_info['allow_modify_subdirectories'] )
{
    $xtpl->parse( 'main.allow_modify_subdirectories' );
}

include ( NV_ROOTDIR . "/includes/header.php" );
if ( $popup )
{
    $xtpl->parse( 'main.header' );
    $xtpl->parse( 'main.footer' );
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );	
	echo $contents;
}
else
{
    $xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	echo nv_admin_theme( $contents );
}
include ( NV_ROOTDIR . "/includes/footer.php" );
/*
# config
$path = ( defined( 'NV_IS_SPADMIN' ) ) ? "" : NV_UPLOADS_DIR;
$path = htmlspecialchars( trim( $nv_Request->get_string( 'path', 'get', $path ) ), ENT_QUOTES );
$currentpath = $nv_Request->isset_request( 'path', 'post' ) ? htmlspecialchars( trim( $nv_Request->get_string( 'path', 'post', $path ) ), ENT_QUOTES ) : htmlspecialchars( trim( $nv_Request->get_string( 'currentpath', 'get', $path ) ), ENT_QUOTES );
if ( empty( $currentpath ) )
{
    $currentpath = NV_UPLOADS_DIR;
}
$area = "";
$popup = $nv_Request->get_int( 'popup', 'get', 0 );
$selectedfile = '';
$uploadflag = $nv_Request->isset_request( 'confirm', 'post' );

if ( $uploadflag )
{
    require_once ( NV_ROOTDIR . "/includes/class/upload.class.php" );
    $upload = new upload( $admin_info['allow_files_type'], $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
    if ( is_uploaded_file( $_FILES['fileupload']['tmp_name'] ) && nv_check_allow_upload_dir( $currentpath ) )
    {
        $upload_info = $upload->save_file( $_FILES['fileupload'], NV_ROOTDIR . '/' . $currentpath, false );
        if ( ! empty( $upload_info['error'] ) )
        {
            $errors[] = $upload_info['error'];
        }
        else
        {
            $selectedfile = $upload_info['basename'];
        }
    }
    elseif ( $nv_Request->isset_request( 'imgurl', 'post' ) and nv_is_url( $nv_Request->get_string( 'imgurl', 'post' ) ) )
    {
        $urlfile = trim( $nv_Request->get_string( 'imgurl', 'post' ) );
        $upload_info = $upload->save_urlfile( $urlfile, NV_ROOTDIR . '/' . $currentpath, false );
        if ( ! empty( $upload_info['error'] ) )
        {
            $errors[] = $upload_info['error'];
        }
        else
        {
            $selectedfile = $upload_info['basename'];
        }
    }
    else
    {
        $errors[] = $lang_module['upload_file_error_invalidurl'];
    }
}

if ( ! empty( $errors ) )
{
    $contents .= "<div id='edit'></div>\n";
    $contents .= "<div class=\"quote\" style=\"width:780px;\">\n";
    $contents .= "<blockquote class='error'><span id='message'>" . implode( "<br>", $errors ) . "</span></blockquote>\n";
    $contents .= "</div>\n";
    $contents .= "<div class=\"clear\"></div>\n";

}
$contents .= '
<table>
<tbody>
		<tr>
			<td valign="top">
				<div name="imgfolder" id="imgfolder" size="25" style="width:200px;height:340px;overflow:auto;cursor:pointer">';
$type = htmlspecialchars( trim( $nv_Request->get_string( 'type', 'get', 'file' ) ), ENT_QUOTES );
$contents .= '	</div>';
$contents .= '
<script type="text/javascript">
$(function(){
	$("#imgfolder").load("' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=folderlist&path=' . $path . '&currentpath=' . $currentpath . '");
	$("div#imglist").html("<span style=\'color:red\'><img src=\'' . NV_BASE_SITEURL . 'images/load.gif\'/> please wait...</span>");
	$("div#imglist").html("<iframe src=\"' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=imglist&path=' . $currentpath . '&type=' . $type . '&imgfile=' . $selectedfile . '\" style=\"width:590px;height:300px;border:none\"></iframe>");
	$("select[name=imgtype]").change(function(){
		var folder = $("span#foldervalue").attr("title");
		var type = $(this).val();
		$("input[name=path]").val(folder);
		$("div#imglist").html("<iframe src=\'' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=imglist&path="+folder+"&type="+type+"\' style=\"width:590px;height:300px;border:none\"></iframe>");		
	});	
});
</script>
			</td>
			<td valign="top">
				<select name="imgtype" id="imgtype" style="margin-left:10px;margin-right:10px;float:right">
				<option value="file" ' . ( ( $type == 'file' ) ? ' selected' : '' ) . '>' . $lang_module['type_file'] . '</option>
				<option value="image" ' . ( ( $type == 'image' ) ? ' selected' : '' ) . '>' . $lang_module['type_image'] . '</option>
				<option value="flash" ' . ( ( $type == 'flash' ) ? ' selected' : '' ) . '>' . $lang_module['type_flash'] . '</option>
				</select><input type="button" id="uploadfile" value="Upload" style="margin-left:10px;"/>
				<form enctype="multipart/form-data" action="" name="uploadimg" id="uploadimg" style="display:none" method="POST">
					<input type="hidden" name="path" value="' . $currentpath . '"/>
					' . $lang_module['upload_file'] . ' <input type="file" name="fileupload"/> ' . $lang_module['upload_otherurl'] . ' <input type="text" name="imgurl"/> <input type="submit" value="GO" name="confirm"/>
				</form>
				<br />
				<div id="imglist" name="imglist" style="height:360px;width:590px;vertical-align:top;padding:10px"></div>
			</td>			
		</tr>
		<tr>
			<td colspan="2">';
if ( $popup )
{
    $area = htmlspecialchars( trim( $nv_Request->get_string( 'area', 'get' ) ), ENT_QUOTES );
}
$contents .= '<img id="image" src="" name="' . $area . '" title="" style="display:none"/>

<script type="text/javascript">
	$("#uploadfile").toggle(function(){
		$("#uploadimg").show();
	}, function(){
		$("#uploadimg").hide();
	});
</script>
			</td>			
		</tr>';

$contents .= '<tr style="display:none" class="formfile">
			<td style="text-align:center" colspan="2">
			<input type="hidden" id="posthidden" value=""/>
			</td>		
		</tr>';
$contents .= '</tbody>
</table>
';
$contents .= '
<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $global_config['admin_theme'] . '/css/admin.css" type="text/css" />
<link type="text/css" href="' . NV_BASE_SITEURL . 'js/ui/jquery.ui.all.css" rel="stylesheet" />
<link type="text/css" href="' . NV_BASE_SITEURL . 'js/jquery/jquery.treeview.css" rel="stylesheet" />
<script type="text/javascript" src="' . NV_BASE_SITEURL . 'js/jquery/jquery.treeview.min.js"></script>
<script type="text/javascript" src="' . NV_BASE_SITEURL . 'js/ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="' . NV_BASE_SITEURL . 'js/ui/jquery.ui.dialog.min.js"></script>
<script type="text/javascript" src="' . NV_BASE_SITEURL . 'js/contextmenu/jquery.contextmenu.r2.js"></script>
<div id="renamefolder" title="' . $lang_module['renamefolder'] . '">' . $lang_module['rename_newname'] . '<input type="text" name="foldername"/></div>
<div id="createfolder" title="' . $lang_module['createfolder'] . '">' . $lang_module['rename_newname'] . '<input type="text" name="createfoldername"/></div>
<script type="text/javascript">
function insertvaluetofield(){
	var value = $("#posthidden").val();
	var funcNum = ' . $nv_Request->get_int( 'CKEditorFuncNum', 'get', 0 ) . ';
	if (funcNum > 0){
		window.opener.CKEDITOR.tools.callFunction(funcNum, value,"");
	}
	else{
		$("#' . $area . '",opener.document).val(value);
	} 
}
$("div#createfolder").dialog({
	autoOpen: false,
	width: 250,
	height: 160,
	modal: true,
	position: "center",
	buttons: {
		Ok: function() {
			var foldervalue = $("span#foldervalue").attr("title");
			var newname = $("input[name=createfoldername]").val();
			if (newname==""){
				alert("' . $lang_module['rename_nonamefolder'] . '");
				$("input[name=foldername]").focus();
				return false;
			}
			$.ajax({
			   type: "POST",
			   url: "' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=createfolder",
			   data: "path="+foldervalue+"&newname="+newname,
			   success: function(data){
			   		$("div#imglist").html("<iframe src=\"' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=imglist&path=' . $currentpath . '&type=' . $type . '\" style=\"width:570px;height:360px;border:none\"></iframe>");
					$("#imgfolder").load("' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=folderlist&path=' . $path . '&currentpath="+data);
			   }
			 });
			$(this).dialog("close");
		}
	}
});
$("div#renamefolder").dialog({
	autoOpen: false,
	width: 250,
	height: 160,
	modal: true,
	position: "center",
	buttons: {
		Ok: function() {
			var foldervalue = $("span#foldervalue").attr("title");
			var newname = $("input[name=foldername]").val();
			if (newname=="" || newname==foldervalue){
				alert("' . $lang_module['rename_nonamefolder'] . '");
				$("input[name=foldername]").focus();
				return false;
			}
			$.ajax({
			   type: "POST",
			   url: "' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=renamefolder",
			   data: "path="+foldervalue+"&newname="+newname,
			   success: function(data){
					$("div#imglist").html("<iframe src=\'' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=imglist&path="+newname+"\' style=\"width:590px;height:360px;border:none\"></iframe>");
					$("#imgfolder").load("' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=folderlist&currentpath="+data);
			   }
			 });
			$(this).dialog("close");
		}
	}
});
</script>
	<span style="display:none" id="foldervalue" title="' . $currentpath . '"></span>
    <div style="display:none" id="folder-menu">
        <ul>';
if ( $admin_info['allow_create_subdirectories'] )
{
    $contents .= '<li id="createfolder"><img src="' . NV_BASE_SITEURL . 'js/contextmenu/icons/copy.png"/>' . $lang_module['createfolder'] . '</li>';
}
if ( $admin_info['allow_modify_subdirectories'] )
{
    $contents .= '<li id="renamefolder"><img src="' . NV_BASE_SITEURL . 'js/contextmenu/icons/rename.png"/>' . $lang_module['renamefolder'] . '</li>
            <li id="deletefolder"><img src="' . NV_BASE_SITEURL . 'js/contextmenu/icons/delete.png"/>' . $lang_module['deletefolder'] . '</li>';
}
$contents .= '
        </ul>
    </div>';
*/
/*include ( NV_ROOTDIR . "/includes/header.php" );
if ( $popup )
{
    echo $contents;
}
else
{
    echo nv_admin_theme( $contents );
}
include ( NV_ROOTDIR . "/includes/footer.php" );*/

?>