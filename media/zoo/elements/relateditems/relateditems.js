/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

function selectRelateditem(e,t,i){jQuery("#"+i).ElementRelatedItems("addItem",e,t),jModalClose()}!function(e){var t=function(){};e.extend(t.prototype,{name:"ElementRelatedItems",options:{variable:null,msgDeleteItem:"Delete Item",msgSortItem:"Sort Item"},initialize:function(t,i){this.options=e.extend({},this.options,i),this.list=t.find("ul").delegate("div.item-delete","click",function(){e(this).closest("li").fadeOut(200,function(){e(this).remove()})}).sortable({handle:"div.item-sort",placeholder:"dragging",axis:"y",opacity:1,delay:100,tolerance:"pointer",containment:"parent",forcePlaceholderSize:!0,scroll:!1,start:function(e,t){t.helper.addClass("ghost")},stop:function(e,t){t.item.removeClass("ghost")}})},addItem:function(t,i){var o=!1;this.list.find("li input").each(function(){e(this).val()==t&&(o=!0)}),o||e('<li><div><div class="item-name">'+i+'</div><div class="item-sort" title="'+this.options.msgSortItem+'"></div><div class="item-delete" title="'+this.options.msgDeleteItem+'"></div><input type="hidden" name="'+this.options.variable+'" value="'+t+'"/></div></li>').appendTo(this.list)}}),e.fn[t.prototype.name]=function(){var i=arguments,o=i[0]?i[0]:null;return this.each(function(){var n=e(this);if(t.prototype[o]&&n.data(t.prototype.name)&&"initialize"!=o)n.data(t.prototype.name)[o].apply(n.data(t.prototype.name),Array.prototype.slice.call(i,1));else if(!o||e.isPlainObject(o)){var a=new t;t.prototype.initialize&&a.initialize.apply(a,e.merge([n],i)),n.data(t.prototype.name,a)}else e.error("Method "+o+" does not exist on jQuery."+t.name)})}}(jQuery);