(function() {  
    tinymce.create('tinymce.plugins.daum_map', {  
        init : function(ed, url) {  
            ed.addButton('daum_map', {
                title : 'Add a Daum map',
                image: url.replace("js","images") + '/daum_map_icon.png',
                onclick : function(){
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = H - 84;
                    tb_show( 'Add a Daum map', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=daum_map-form' );
                }
            });
            /*ed.addCommand('insertdaummap', function() {
                        ed.windowManager.open({
                            title : 'Add a Daum map',
                            file : ajaxurl+'?action=daum_map_form', // file that contains HTML for our modal window
                            width : 320 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
                            height : 320 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
                            inline : 1
                        }, {
                            plugin_url : url
                        });
                    });
                    url = url.replace("js","images");        
                    // Register buttons
                    ed.addButton('daum_map', {title : 'Add a Daum map', cmd : 'insertdaummap', image: url + '/daum_map_icon.png' }); */
        },
        getInfo : function() {
            return {
                longname : 'Insert Daum Map',
                author : 'Stackr Inc.',
                authorurl : 'http://www.stackr.co.kr',
                infourl : 'http://www.stackr.co.kr',
                version : tinymce.majorVersion + "." + tinymce.minorVersion
            };
        },
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('daum_map', tinymce.plugins.daum_map);  
     jQuery(function(){
        /*jQuery.post(
                ajaxurl,
                {
                    action:'daum_map_api_check'
                },
                function(res){
                    alert(res);
                }
            );*/
        // creates a form to be displayed everytime the button is clicked
        // you should achieve this using AJAX instead of direct html code like this
        
        if(!is_daum_map){
            var form = jQuery('<div id="daum_map-form"><table id="daum_map-table" class="form-table">\
                <tr>\
                    <th><label for="address-columns">에러</label></th>\
                    <td>다음 API를 설정해주세요.</td>\
                </tr>\
            </table>\
            </div>');
        }else{
            var form = jQuery('<div id="daum_map-form"><table id="daum_map-table" class="form-table">\
                <tr>\
                    <th><label for="address-columns">주소</label></th>\
                    <td><input type="text" id="address-columns" name="address" value="" /><button type="submit" class="get_addr2coord">주소검색</button><br />\
                    <small>주소를 입력해주세요.</small><div class="result_addr2coord"></div></td>\
                </tr>\
                <tr>\
                    <th><label for="lat-columns">LAT</label></th>\
                    <td><input type="text" id="lat-columns" value="" readonly/></td>\
                </tr>\
                <tr>\
                    <th><label for="lng-columns">LNG</label></th>\
                    <td><input type="text" id="lng-columns" value="" readonly/></td>\
                </tr>\
                <tr>\
                    <th><label for="mapw-columns">가로사이즈</label></th>\
                    <td><input type="text" id="mapw-columns" value="320"/></td>\
                </tr>\
                <tr>\
                    <th><label for="maph-columns">세로사이즈</label></th>\
                    <td><input type="text" id="maph-columns" value="320"/></td>\
                </tr>\
                <tr>\
                    <th><label for="marker-columns">마커추가</label></th>\
                    <td><input type="checkbox" id="marker-columns" value="Y"/></td>\
                </tr>\
                <tr>\
                    <th><label for="info-columns">주소 표시</label></th>\
                    <td><input type="checkbox" id="info-columns" value="Y"/></td>\
                </tr>\
            </table>\
            <p class="submit">\
                <input type="button" id="daum_map-submit" class="button-primary" value="Insert Daum Map" name="submit" />\
            </p>\
            </div>');
        }
        
        
        var table = form.find('table');
        form.appendTo('body').hide();
        
        // handles the click event of the submit button
        form.find('#daum_map-submit').click(function(){
            // defines the options and their default values
            // again, this is not the most elegant way to do this
            // but well, this gets the job done nonetheless
            var options = { 
                'address'       : '',
                'lat'           : '',
                'lng'           : '',
                'mapw'         : '320',
                'maph'        : '320',
                'marker'        : 'N',
                'info'          : 'N'
                };
            var shortcode = '[daum_map';
            
            for( var index in options) {console.log(index);
                var value = table.find('#' + index +'-columns').val();

                if((index == 'lat' && value == '') || (index == 'lng' && value == '')){
                    alert('주소검색을 하여 지도 좌표를 구해주세요.');
                    return false;
                }
                
                // attaches the attribute to the shortcode only if it's different from the default value
                //if ( value !== options[index] )
                if( value == '')
                    shortcode += ' ' + index + '="' + options[index] + '"';
                else
                    shortcode += ' ' + index + '="' + value + '"';
            }
            
            shortcode += ']';
            
            // inserts the shortcode into the active editor
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
            
            // closes Thickbox
            tb_remove();
        });
    });
})();
jQuery(document).ready(function($){
    $('#daum_map-form button.get_addr2coord').bind('click',function(){
        var address = $(this).parent().find('input#address-columns');
        if(address.val() == ''){
            alert('주소를 입력해주세요.');
            address.focus();
            return false;
        }
        $.post(
                ajaxurl,
                {
                    action:'get_addr2coord',
                    address:address.val()
                },
                function(res){
                    $('.result_addr2coord').html(res.html);
                },
                'json'
            );
        return false;
    });
    $('#lng-columns, #lat-columns').bind('click',function(){
        alert('주소 검색을 이용해주세요.');
    });
    $('div.result_addr2coord .page a').live('click',function(){
        var address = $('div.get_addr2coord input[name="address"]');
        var pageno = $(this).html();

        $.post(
                ajaxurl,
                {
                    action:'get_addr2coord',
                    address:address.val(),
                    pageno:pageno
                },
                function(res){
                    $('.result_addr2coord').html(res.html);
                },
                'json'
            );

        return false;
    });
    $('div.result_addr2coord li').live('click',function(){
        var lat = $(this).attr('data-y');
        var lng = $(this).attr('data-x');

        $('input#address-columns').val($(this).html());
        $('#lng-columns').val(lng);
        $('#lat-columns').val(lat);
        $('div.result_addr2coord').html('');
        return false;
    });

});