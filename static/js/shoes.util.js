var click = false;
// 이중 클릭 방지 없음 - 사용법은 ax_post 와 동일
function ex_post(url, data, callback, callback_done ,callback_fail, datatype){
    if(!callback){
        callback = function(ret){
            if(ret.result == 'ok'){
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('success');
                }
            }else{
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('fail');
                }
            }
        }
    }
    if(!datatype){
        datatype = 'json'
    }
    jQuery.post(url, data, callback, datatype)
        .done(function(ret){
            if(callback_done) callback_done(ret);
        })
        .fail(function(){
            if(callback_fail) callback_fail();
        });
}

// 이중 클릭 방지
function ax_post(url, data, callback, callback_done ,callback_fail, datatype){
    if(!callback){
        callback = function(ret){
            if(ret.result == 'ok'){
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('success');
                }
            }else{
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('fail');
                }
            }
        }
    }
    if(!datatype){
        datatype = 'json'
    }
    if(click) {
        return false;
    }
    click = true;
    //jQuery("button, .btn").attr("disabled","disabled");
    jQuery.post(url, data, callback, datatype)
        .done(function(ret){
            click = false;
            if(callback_done) callback_done(ret);
            //jQuery("button, .btn").removeAttr("disabled");
        })
        .fail(function(){
            click = false;
            if(callback_fail) callback_fail();
        });
}

// 이중 클릭 방지
function ax_get(url, callback, callback_done, callback_fail, datatype){
    if(!datatype){
        datatype = 'json'
    }
    click = true;
    jQuery.get(url, callback, datatype)
        .done(function(){
            click = false;
            if(callback_done) callback_done();
        })
        .fail(function(){
            click = false;
            if(callback_fail) callback_fail();
        });
}

function ax_get_html(url, callback, callback_done, callback_fail){
    ax_get(url, callback, callback_done, callback_fail, 'html');
}

(function(jQuery){
    jQuery.fn.setModal = function(options){
        var settings = jQuery.extend({
            url : "",
            width : "580",
            max_height : "400",
            overflow : "scroll"
        }, options)
        jQuery(document).on("click", this.selector ,function(){
            var url = settings.url;
            if(!settings.url){
                url = jQuery(this).data('url');
            }

            var target_id = jQuery(this).data('target');
            var callback = function(d){
                jQuery(target_id).find(".modal-content").html(d).end()
                            .find(".modal-dialog").css("width",settings.width+"px").end()
                            .find(".modal-body").css({
                                "max-height" : settings.max_height+"px",
                                "overflow-y" : settings.overflow
                            });
            }
            ax_get_html(url, callback);
        });
        return this;
    };
}(jQuery));

(function(jQuery){
    var modal_html = '<div class="modal fade" id="pop_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><center><img src="/static/img/loading.gif"/></center></div></div></div>';

    jQuery.fn.setPopup = function(options){
        // console.log(options);
        var settings = jQuery.extend({
            url : "",
            srl_name : "srl",
            add_param_id : "",              // add_param으로 몰아주세요
            add_param_required : false,     // add_param으로 몰아주세요
            add_param : [],             //[{id:'', 'required':true}, {id:'', 'required':true}, ...]
            width : "620",
            max_height : "400",
            overflow : "scroll",
            select_type : "radio", // radio: 1 choice, checkbox: multiple choice, callback_only: callback 호출만 하는 팝업
            callback : function(){}
        }, options);
        this.css('cursor','pointer');
        // add_param_id, add_param_required를 add_param으로 몰기위한 코드
        if(settings.add_param_id !== ''){
            settings.add_param.push({
                "id" : settings.add_param_id,
                "required" : settings.add_param_required
            });
        }

        // array가 아니고 object가 들어오면 array로 변환
        if(settings.add_param.constructor !== Array){
            settings.add_param = [settings.add_param];
        }

        jQuery(document).off("click", this.selector);
        jQuery(document).on("click", this.selector ,function(){

            // 필수 체크
            for (var i = settings.add_param.length - 1; i >= 0; i--) {
                var param = settings.add_param[i];
                if(param.required && !jQuery("#"+param.id).val()){
                    alert('select required field first');
                    return false;
                }
            };

            var input_box = jQuery(this);
            input_box.after(modal_html);
            
            var pop_modal = jQuery("#pop_modal");
            pop_modal.modal({
                show: 'false'
            }).on('hidden.bs.modal', function(){
                jQuery(this).remove();
            });
            var callback = function(d){
                pop_modal.find(".modal-content").html(d).end()
                            .find(".modal-dialog").css("width",settings.width+"px").end()
                            .find(".modal-body").css({
                                "max-height" : settings.max_height+"px",
                                "overflow-y" : settings.overflow
                            }).end();


                // default 는 radio 방식 (1개만 선택 가능)
                if(settings.select_type == 'radio'){
                    pop_modal.find(".modal-content").on("click", ".pop-select-btn", function(event){
                        var data = jQuery(this).data();
                        var label = data.label;
                        var srl = data.srl;
                        // if(input_box.prev().is("[name="+settings.srl_name+"]")){
                        //     input_box.prev().remove();
                        // }
                        if(input_box.parent().find('[name="'+settings.srl_name+'"]').length >0){
                            input_box.parent().find('[name="'+settings.srl_name+'"]').remove();
                        }
                        input_box.val(label)
                                .before('<input type="hidden" id="'+settings.srl_name+'" name="'+settings.srl_name+'" value="'+srl+'"/>');
                        pop_modal.modal("toggle");
                        settings.callback(data);
                        input_box.trigger("change");
                        event.preventDefault();
                    });
                }
                // checkbox의 경우 return-check-btn을 사용해 전달 : checkbox name="check_'+settings.srl_name+'" 으로 고정
                else if(settings.select_type == 'checkbox'){
                    pop_modal.find(".modal-content").on("click", "#return-check-btn", function(event){
                        // 클릭한 input 상위DOM 안에 포함된 srl_name 인 input 삭제
                        input_box.parent().find('input[name="'+settings.srl_name+'"]').remove();
                        var checked_srl = [];
                        var return_data = [];

                        pop_modal.find('input:checkbox[name="check_'+settings.srl_name+'"]:checked').each(function(){
                            var data = jQuery(this).data();
                            var srl = data.srl;
                            
                            return_data.push(data);
                            checked_srl.push(data.label);
                            input_box.before('<input type="hidden" name="'+settings.srl_name+'" value="'+srl+'"/>');
                        });

                        input_box.val(checked_srl.join(', '));
                        pop_modal.modal("toggle");
                        settings.callback(return_data);
                        input_box.trigger("change");
                        event.preventDefault();
                    });
                }
                else if(settings.select_type == 'callback_only'){
                    pop_modal.find(".modal-content").on("click", ".pop-select-btn", function(event){
                        var data = jQuery(this).data();
                        pop_modal.modal("toggle");
                        settings.callback(data);
                        input_box.trigger("change");
                        event.preventDefault();
                    });
                }
            };
            var url = settings.url;
            // 필수 체크
            for (var i = settings.add_param.length - 1; i >= 0; i--) {
                var param = settings.add_param[i];
                if(param.id !== '' && jQuery("#"+param.id).val()){
                    url = UpdateQueryString(url, param.id, jQuery("#"+param.id).val());
                }
            };

            ax_get_html(url, callback);
        });
        if(this.next().is('.input-group-addon, .input-group-btn')){
            var input_box = this;
            this.next().click(function(){
                input_box.val('');

                if(input_box.parent().find('[name="'+settings.srl_name+'"]').length >0){
                    input_box.parent().find('[name="'+settings.srl_name+'"]').remove();
                }
                input_box.trigger("change");
            });
        }
        return this;
    };
}(jQuery));

function UpdateQueryString(url, key, value) {
    if (!url) url = window.location.href;
    var re = new RegExp("([?&])" + key + "=.*?(&|#|jQuery)(.*)", "gi"),
        hash;

    if (re.test(url)) {
        if (typeof value !== 'undefined' && value !== null)
            return url.replace(re, 'jQuery1' + key + "=" + value + 'jQuery2jQuery3');
        else {
            hash = url.split('#');
            url = hash[0].replace(re, 'jQuery1jQuery3').replace(/(&|\?)jQuery/, '');
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        }
    }
    else {
        if (typeof value !== 'undefined' && value !== null) {
            var separator = url.indexOf('?') !== -1 ? '&' : '?';
            hash = url.split('#');
            url = hash[0] + separator + key + '=' + value;
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        }
        else
            return url;
    }
}

//ex jQuery("input.number").digits();
jQuery.fn.digits = function(){ 
    jQuery(this).keyup(function(event) {

        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        jQuery(this).val(function(index, value) {
            return value.replace(/\D/g, "")
                        .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
        });
    });
}

//ex jQuery("input.number").digits();
jQuery.fn.commify = function(){ 
    // format number
    jQuery(this).html(function(index, value) {
        return value.replace(/\D/g, "")
                    .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
    });
}

function commify(num){
    return num.toString().replace(/\D/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
}

// javascript in_array
function in_array(needle, haystack, argStrict) {
  //  discuss at: http://phpjs.org/functions/in_array/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: vlado houba
  // improved by: Jonas Sciangula Street (Joni2Back)
  //    input by: Billy
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: true
  //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
  //   returns 2: false
  //   example 3: in_array(1, ['1', '2', '3']);
  //   example 3: in_array(1, ['1', '2', '3'], false);
  //   returns 3: true
  //   returns 3: true
  //   example 4: in_array(1, ['1', '2', '3'], true);
  //   returns 4: false

  var key = '',
    strict = !! argStrict;

  //we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] == ndl)
  //in just one for, in order to improve the performance 
  //deciding wich type of comparation will do before walk array
  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true;
      }
    }
  } else {
    for (key in haystack) {
      if (haystack[key] == needle) {
        return true;
      }
    }
  }

  return false;
}



/*
    엔터로 버튼 클릭하기...
    jQuery("#sg_name").enterTrigger("#search_btn");
    sg_name(아마 input)에 값을 넣고 엔터를 치면 search_btn을 클릭한 것과 같은 효과
*/
(function(jQuery){
    jQuery.fn.enterTrigger = function(options){
        var settings = jQuery.extend({
            btn : "#search_btn",
        }, options)
        jQuery(document).on("keypress", this.selector ,function(e){
            if(e.which == 13){
                jQuery(settings.btn).trigger("click");
                e.preventDefault();
            }
        });
        return this;
    };
}(jQuery));

/*
    에디터...붙이고.... 값 가져오기
    set 할 때 : jQuery("textarea[name=contents]").addEditor();
    ajax쏘기 전에 : jQuery("textarea[name=contents]").getText();
    form submit이면 알아서 해주는데... ajax로 날리기 전에는... 텍스트를 .val() 해주는게 꼭 필요함 ㅠ_ㅠ
    sample : views/cms/item_form_step2.php
*/
(function(jQuery){
    jQuery.fn.addEditor = function(options){
        var settings = jQuery.extend({
            height: 300,
            menubar: false,
            statusbar : false,
            plugins: ["table textcolor colorpicker link image code"],
            toolbar_items_size: "small",
            toolbar1: "newdocument | cut copy paste | bullist numlist | alignleft aligncenter alignright alignjustify | outdent indent blockquote | link unlink image media code",
            toolbar2: "table | bold italic underline strikethrough | forecolor backcolor | styleselect formatselect fontselect fontsizeselect",

        }, options);
        tinymce.init({
            selector: this.selector,
            height: settings.height,

            menubar: settings.menubar,
            statusbar : settings.statusbar,
            plugins: settings.plugins,

            toolbar_items_size: settings.toolbar_items_size,
            toolbar1: settings.toolbar1,
            toolbar2: settings.toolbar2
        });
        return this;
    };
    jQuery.fn.getText = function(){
        var contents = tinymce.get(jQuery(this).attr('name')).getContent();
        jQuery(this.selector).val(contents);

        return this;
    };
}(jQuery));

/*
    ik 붙이기
*/
function add_ik(str){
    if(str){
        if(str.toLowerCase().indexOf("ik") !== 0){
            str = "ik" + str;
        }
    }
    return str;
}
/*
    ik 떼기
*/
function remove_ik(str){
    if(str){
        if(str.toLowerCase().indexOf("ik") === 0){
            str = str.substring(2);
        }
    }
    return str;
}
/*
    지역 select
    하위지역셀렉트.regionSelect(상위지역셀렉트, [선택된 하위지역 코드]);
    jQuery('#comp_regi_code').regionSelect('#comp_parent_regi_code', 'ink-r-0224');
*/
(function(jQuery){
    jQuery.fn.regionSelect = function(parent_selector, options){
        var settings = jQuery.extend({
            selected_regi_code : "",
            select_region_dom : '<option value=""> Select region </option>',
            parent_select_region_dom : '<option value=""> Select region </option>',
            add_all : false
        }, options)
        var select_region_dom = settings.select_region_dom;
        var selected_regi_code = settings.selected_regi_code;
        var parent_select_region_dom = settings.parent_select_region_dom;
        var child = jQuery(this);
        var parent = jQuery(parent_selector);
        var url = "/common/region/ax_get_all_regions";
        var all_region;
        var selected_parent_regi_code = '';

        ax_get(url, function(ret){
            all_region = ret;
            child.html(select_region_dom);
            parent.html(parent_select_region_dom);
            for(parent_key in all_region){
                var selected = '';
                if(selected_regi_code){
                    for(children_key in all_region[parent_key]['children']){
                        if(children_key == selected_regi_code){
                            selected_parent_regi_code = parent_key;
                            break;
                        }
                    }
                }
                parent.append('<option value="'+parent_key+'">'+all_region[parent_key]['regi_name']+'</option>');
            }
            if(selected_parent_regi_code != ''){
                parent.val(selected_parent_regi_code).trigger("change");
                child.val(selected_regi_code);
            }
        });

        parent.change(function(){
            child.html(select_region_dom);
            var parent_code = parent.val();
            if(parent_code !=''){
                var children = all_region[parent_code]['children'];
                if(settings.add_all)
                    child.append('<option value="all">ALL</option>');
                for (children_key in children) {
                    child.append('<option value="'+children_key+'">'+children[children_key]['regi_name']+'</option>');
                };
            }
        });
    };
}(jQuery));
