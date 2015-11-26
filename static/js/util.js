function ax_post(url, data, callback, callback_done ,callback_fail, datatype){
    if(!callback){
        callback = function(ret){
            if(ret.result == 'ok'){
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('성공');
                }
            }else{
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('실패');
                }
            }
        }
    }
    if(!datatype){
        datatype = 'json'
    }
    $("button, .btn").attr("disabled","disabled");

    $.post(url, data, callback, datatype)
        .done(function(ret){
            if(callback_done) callback_done(ret);
            $("button, .btn").removeAttr("disabled");
        })
        .fail(function(){
            if(callback_fail) callback_fail();
        });
}

function ax_post_file(url, data, callback, callback_done ,callback_fail, datatype){
    if(!callback){ //success
        callback = function(ret){
            if(ret.result == 'ok'){
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('성공');
                }
            }else{
                if(ret.msg){
                    alert(ret.msg);
                }else{
                    alert('실패');
                }
            }
        }
    }
    if(!datatype){
        datatype = 'json'
    }

    $("button, .btn").attr("disabled","disabled");

    $.ajax({
        'url': url,
        'type': 'POST',
        'dataType': datatype,
        'contentType': false,
        'processData': false,
        'data': data,
        'success': callback
    })
    .done(function(ret){
        if(callback_done) callback_done(ret);
        $("button, .btn").removeAttr("disabled");
    })
    .fail(function(){
        if(callback_fail) callback_fail();
    });
}

function ax_get(url, callback, callback_done, callback_fail, datatype){
    if(!datatype){
        datatype = 'json'
    }
    $.get(url, callback, datatype)
        .done(function(data){
            if(callback_done) callback_done(data);
        })
        .fail(function(){
            if(callback_fail) callback_fail();
        });
}

function in_array(needle, haystack, argStrict) {
  //  discuss at: http://phpjs.org/functions/in_array/
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

//ex url = update_query_string(url, 'a', 'b') => url?a=b
function update_query_string(url, key, value) {
    if (!url) url = window.location.href;
    var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
        hash;

    if (re.test(url)) {
        if (typeof value !== 'undefined' && value !== null)
            return url.replace(re, '$1' + key + "=" + value + '$2$3');
        else {
            hash = url.split('#');
            url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
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

//ex $("input.number").digits();
$.fn.digits = function(){ 
    $(this).keyup(function(event) {

        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
            return value.replace(/\D/g, "")
                        .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        });
    });
}

//ex var new_value = commify(old_value);
function commify(value){
    return value.replace(/\D/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
}

/*
    엔터로 버튼 클릭하기...
    $("#sg_name").enterTrigger("#search_btn");
    sg_name(아마 input)에 값을 넣고 엔터를 치면 search_btn을 클릭한 것과 같은 효과
*/
(function($){
    $.fn.enterTrigger = function(options){
        var settings = $.extend({
            btn : "#search_btn",
        }, options)
        $(document).on("keypress", this.selector ,function(e){
            if(e.which == 13){
                $(settings.btn).trigger("click");
                e.preventDefault();
            }
        });
        return this;
    };
}(jQuery));


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