<style>

.addwindow {
	
	position:relative:
	border: 2px;
	display: none;
	
}
.horizontalitemfull<?php echo $id; ?> {
	
	margin: 10px;
	padding: 10px;
	border: 2px solid #555;
	<?php
	if($_POST['id']!=$id) {
		echo 'display: none;';
	}
	?>
	
}


.sliderdelete<?php echo $id; ?>{
	
	
	display: none;
	
}
.edititem {
	
	position:relative:
	border: 2px;
	display: none;
	margin: 8px;
	line-height:250%;
	padding: 8px;
	background-color:#CCC;
	
}
</style>
    <script type="text/javascript">

        jQuery(document).ready( function () { 
		
		
		var uploadID<?php echo $id; ?> = ''; /*setup the var in a global scope*/

jQuery('.upload-button<?php echo $id; ?>').click(function() {
	
	

//uploadID = jQuery(this).prev('input');
uploadID<?php echo $id; ?> = jQuery(this).prev('input');


window.send_to_editor = function(html) {
	

imgurl = jQuery('img',html).attr('src');

uploadID<?php echo $id; ?>.val(imgurl)
tb_remove();
}


tb_show('', 'media-upload.php?type=image&amp;amp;amp;amp;TB_iframe=true&uploadID<?php echo $id; ?>=' + uploadID<?php echo $id; ?>);

return false;
});



		
		
          
	jQuery('.editslider<?php echo $id; ?>').click(function() {
		
		
	if(jQuery('.horizontalitemfull<?php echo $id; ?>').css("display")=="none") jQuery('.horizontalitemfull<?php echo $id; ?>').show();
	else jQuery('.horizontalitemfull<?php echo $id; ?>').css("display", "none")
	
	
	
	return false;
	
	
})
	

	jQuery('.deletebuton<?php echo $id; ?>').click(function() {
		
		
		
			if(jQuery('.sliderdelete<?php echo $id; ?>').css("display")=="none") jQuery('.sliderdelete<?php echo $id; ?>').show();
	else jQuery('.sliderdelete<?php echo $id; ?>').css("display", "none")
		

	
	
	
	return false;
	
	
})	
		 
	jQuery('.additem').click(function() {
		
		
		
	//jQuery('.widget-wp_sliderpro-__i__-savewidget').trigger('click');
	jQuery('input[name=operation]').val('1');
	jQuery('.addwindow').show();
	
	
	
	return false;
	
	
})

	jQuery('#deleteitem<?php echo $id; ?>').click(function() {
		
		
		
	//jQuery('.widget-wp_sliderpro-__i__-savewidget').trigger('click');
	jQuery('input[name=operation]').val('2');
	jQuery('.addwindow').show();
	
	
	
	return false;
	
	
})

	jQuery('.cancel').click(function() {
		
		
		
	//jQuery('.widget-wp_sliderpro-__i__-savewidget').trigger('click');
	jQuery('input[name=operation]').val('0');
	jQuery('.addwindow').hide();
	
	
	
	return false;
	
	
})

jQuery('.<?php echo $id; ?>editbutton').click(function() {
		
		
		
	//jQuery('.widget-wp_sliderpro-__i__-savewidget').trigger('click');
	

	if(jQuery('#<?php echo $id; ?>edit'+jQuery(this).attr("rel")).css("display")=="none") { 
		
		jQuery('#<?php echo $id; ?>edit'+jQuery(this).attr("rel")).show()
		jQuery(this).text("-")
	}
	else { 
		jQuery(this).text('+')
		jQuery('#<?php echo $id; ?>edit'+jQuery(this).attr("rel")).css("display", "none")
	}
	return false;
	
	
})

		  
       
		
		
		
		// media library functions

//var clicked = null;

jQuery('.upload_image_button<?php echo $id; ?>').click(function() {
 

 clicked = jQuery(this);
 
 formfield = jQuery(this).prev('input').attr('name');
 
 
 
if(formfield.search("vid")!=-1) tb_show('', 'media-upload.php?type=video&amp;TB_iframe=true');
else tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 
 return false;
});

window.send_to_editor = function(html) {
 //imgurl = jQuery('img',html).attr('src');
 //clicked.prev('input').val(imgurl);
 //tb_remove();
 
 
 var textt=html;


if(textt.search("img")!=-1) imgurl = jQuery('img',html).attr('src');

else {

	imgurl = jQuery(html).attr('href');

}


//clicked.prev('input').val(imgurl);
+
clicked.prev('input').val(imgurl);

tb_remove();
 
 
 
}

		  
        });

    </script>


	  <legend><strong>Table <?php echo $id; ?>.</strong> To insert into a page or post type:
      
      <input type="text"  value="[ultimatetables <?php echo $id; ?> /]" readonly />. You can also use the ultimate tables widget to place the table in a widget. <button class="editslider<?php echo $id; ?>">EDIT</button></legend> 



<div class="horizontalitemfull<?php echo $id; ?> tablespanel"> 
	<form method="post" action="">
		<fieldset>
<label >Title: </label><input id="stitle<?php echo $id; ?>" name="stitle<?php echo $id; ?>" type="text" value="<?php echo $title; ?>" />
              <hr />
             
              <input name="operation" type="hidden" id="operation" value="0" />
               <input name="itemselect<?php echo $id; ?>" type="hidden" id="itemselect<?php echo $id; ?>" value="" />
           
            
<h3>Table configuration:</h3>          


                      
<label>Table class: </label>     
       
			      
                   <select name="time<?php echo $id; ?>" id="time<?php echo $id; ?>">
                   
                   <option value="display" <?php if($time=="display") echo ' selected="selected"'; ?>>display</option>
                   
			        <option value="bordered" <?php if($time=="bordered") echo ' selected="selected"'; ?>>bordered</option>
					<option value="compact" <?php if($time=="compact") echo ' selected="selected"'; ?>>compact</option>
					
					<option value="hover" <?php if($time=="hover") echo ' selected="selected"'; ?>>hover</option>
					<option value="order-column" <?php if($time=="order-column") echo ' selected="selected"'; ?>>order-column</option>
					<option value="row-border" <?php if($time=="row-border") echo ' selected="selected"'; ?>>row-border</option>
					<option value="stripe" <?php if($time=="stripe") echo ' selected="selected"'; ?>>stripe</option>
					
					
					
			        <option value="zebra" <?php if($time=="zebra") echo ' selected="selected"'; ?>>zebra</option>
                    <option value="manual" <?php if($time=="manual") echo ' selected="selected"'; ?>>manual</option>
                    <option value="rwd-table" <?php if($time=="rwd-table") echo ' selected="selected"'; ?>>rwd-table</option>
                     <option value="rwd-tablegreen" <?php if($time=="rwd-tablegreen") echo ' selected="selected"'; ?>>rwd-tablegreen</option>
                    <option value="" <?php if($time=="") echo ' selected="selected"'; ?>>none</option>
			      
		          </select>
                   <label>Manual class: </label> <input type='text' id='op4<?php echo $id; ?>'  name='op4<?php echo $id; ?>'  value='<?php echo $op4; ?>' >        
              Type or select a predefined class of "ultimate-tables.css".<br/>
              
              
              
              <label >Max Height: </label><input id="theight<?php echo $id; ?>" name="theight<?php echo $id; ?>" type="text" value="<?php echo $theight; ?>" /> Blank value predetermined height || 
              
              <label>Auto width</label>
                <select name="sizedescription<?php echo $id; ?>" id="sizedescription<?php echo $id; ?>">
			        <option value="true" <?php if($sizedescription=="true" || $sizedescription=="") echo ' selected="selected"'; ?>>True</option>
			        <option value="false" <?php if($sizedescription=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select> 
              <br/>
               
               
           
                   <label>Pagination</label>
			      <select name="sizethumbnail<?php echo $id; ?>" id="sizethumbnail<?php echo $id; ?>">
			        <option value="true" <?php if($sizethumbnail=="true") echo ' selected="selected"'; ?>>Two buttons</option>
                     <option value="full_numbers" <?php if($sizethumbnail=="full_numbers" || $sizethumbnail=="") echo ' selected="selected"'; ?>>Full numbers</option>
			        <option value="false" <?php if($sizethumbnail=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select>  
                  
                                    <label>Pagination Length change</label>
			      <select name="op5<?php echo $id; ?>" id="op5<?php echo $id; ?>">
			        <option value="true" <?php if($op5=="true" || $op5=="") echo ' selected="selected"'; ?>>True</option>
			        <option value="false" <?php if($op5=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select> 
                  
                  <label>Search</label>
			      <select name="op1<?php echo $id; ?>" id="op1<?php echo $id; ?>">
			        <option value="true" <?php if($op1=="true" || $op1=="") echo ' selected="selected"'; ?>>True</option>
			        <option value="false" <?php if($op1=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select> 
                  
                                    <label>Sort</label>
			      <select name="op2<?php echo $id; ?>" id="op2<?php echo $id; ?>">
			        <option value="true" <?php if($op2=="true" || $op2=="") echo ' selected="selected"'; ?>>True</option>
			        <option value="false" <?php if($op2=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select> 
                  
                                    <label>Info</label>
			      <select name="op3<?php echo $id; ?>" id="op3<?php echo $id; ?>">
			        <option value="true" <?php if($op3=="true" || $op3=="") echo ' selected="selected"'; ?>>True</option>
			        <option value="false" <?php if($op3=="false") echo ' selected="selected"'; ?>>False</option>
			      
		          </select>
				  
				                <label>Conflicts with other jquery functions?</label>
                <select name="color1<?php echo $id; ?>" id="color1<?php echo $id; ?>">
			        <option value="false" <?php if($color1=="false" || $color1=="") echo ' selected="selected"'; ?>>False</option>
			        <option value="true" <?php if($color1=="true") echo ' selected="selected"'; ?>>True</option>
			      
		          </select> 
              <br/>
                  <br/>
            <input type='submit' name='' class='button-primary' value='SAVE TABLE' /> <hr /> 
      
      <input type='hidden' id='twidth<?php echo $id; ?>'  name='twidth<?php echo $id; ?>'  value='<?php echo $width; ?>' />
        
      
      <h3>Table values:</h3>
      <center>
       <label>Columns: </label> <input type='text' id='width<?php echo $id; ?>'  name='width<?php echo $id; ?>'  value='<?php echo $width; ?>' size="6"/>
            
       		<label>Items: </label> <input type='text' id='height<?php echo $id; ?>'  name='height<?php echo $id; ?>'  value='<?php echo $height; ?>' size="6"/>
            <button id="deleteitem<?php echo $id; ?>" name="deleteitem<?php echo $id; ?>" class='button-secondary'>Delete selected</button>
            <div class="addwindow">
             <hr />
           <input type="submit" name="deleteitems" id="deleteitems" value="OK" /><button class="cancel">Cancel</button>
             <hr />
            </div>
      <input type='submit' name='new' id='new' class='button-secondary' value='New Item' /> <input type='submit' name='' class='button-primary' value='SAVE TABLE' /></center><br/><br/>
      
      
      
      <?php
	  
	
	  
	  $items=explode("kh6gfd57hgg", $values);
									
	  
	  
	  echo '
	  <table id="table_'.$id.'" class="zebra">
    <thead>
        <tr>
		<th style="display:none"></th>
		<th width="80px">Order</th>
		';
		
		
		$cc=0;
		$cont=0;
		$cont2=0;
		

		while($cc<$width) {
			
			if(isset($items[$cont2]) && ($cc<$twidth)) {
				$item=explode("t6r4nd", $items[$cont2]);
				$cont2++;
			
				echo '<th><span style="display: none !important;">'.$item[0].'</span><input name="del'.$cc.'" type="checkbox" value="hide" />&nbsp;<input name="order'.$cont.'" type="text" value="'.$cont.'" size="4"/><textarea name="title'.$cont.'" id="title'.$cont.'" style="width:100%">'.$item[0].'</textarea></th>';
			}
			else echo '<th><input name="del'.$cc.'" type="checkbox" value="hide" /><input name="order'.$cont.'" type="text" value="'.$cont.'" size="4"/><textarea name="title'.$cont.'" id="title'.$cont.'" style="width:100%"></textarea></th>';
			$cc++;
			$cont++;
			
		}
		
		while($cc<$twidth) {
			$cont2++;
			$cc++;
		}
	
        echo '</tr>
    </thead>
    <tbody>
	';
	
	
		$cr=0;
		while($cr<$height) {
			
			echo '<tr>';
			
			echo '<td style="display:none">'.$cr.'</td><td><input name="dele'.$cr.'" type="checkbox" value="hide" />&nbsp;<input name="orderc'.$cr.'" type="text" value="'.$cr.'" size="3"/></td>';
			
			
			$cc=0;
			
		while($cc<$width) {
			
			if(isset($items[$cont2]) && ($cc<$twidth)) {
				$item=explode("t6r4nd", $items[$cont2]);
				$cont2++;
				
				
				echo '<td><span style="display: none !important;">'.$item[0].'</span><textarea name="title'.$cont.'" id="title'.$cont.'" style="width:100%">'.$item[0].'</textarea></td>';
			}
			else echo '<td><textarea name="title'.$cont.'" id="title'.$cont.'" style="width:100%"></textarea></td>';
			
			$cont++;
			$cc++;
		}
		
		while($cc<$twidth) {
			$cont2++;
			$cc++;
		}
			
			echo '</tr>';
			$cr++;
		}
		
		echo '
    </tbody>
</table>
	  ';
	  
	  echo '<input class="widefat" name="total" type="hidden" id="total" value="'.$cont.'" />';
	  
	  ?>
      <br/>
     <center> <input type='submit' name='' class='button-primary' value='SAVE TABLE' /></center>
<br/><hr/>

<h3>Texts:</h3>


                    <textarea name="sizetitle<?php echo $id; ?>" rows="10" cols="120" id="sizetitle<?php echo $id; ?>" ><?php echo $sizetitle; ?></textarea>

            

<input type="hidden" name="ultimate_table_nonce_update" id="ultimate_table_nonce_update" value="<?php echo wp_create_nonce( 'ultimate_table_update-' . $id ); ?>">

<br/><br/>
<input name="id" type="hidden" id="id" value="<?php echo $id; ?>" /></td>
	<input type='submit' name='' class='button-primary' value='SAVE TABLE' />
		 
      </fieldset>
	</form>		 <br/>
    <hr />
  <form method="post" action="">
	  <input name="borrar" type="hidden" id="borrar" value="<?php echo $id; ?>">
      <button class="deletebuton<?php echo $id; ?>">Delete table</button>
      <div class="sliderdelete<?php echo $id; ?>">
      <button class="deletebuton<?php echo $id; ?>">CANCEL</button>
      <input type="hidden" name="ultimate_table_nonce_delete" id="ultimate_table_nonce_delete" value="<?php echo wp_create_nonce( 'ultimate_table_delete-' . $id ); ?>">
     <input type='submit' name='' value='OK' />
     </div>
    </form>
  <hr />
  </div>