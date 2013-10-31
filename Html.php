<?php
/*
 * Created on Oct 31, 2013
 *
 * @author Lloyd Zhou
 * @email lloyd.zhou@newbiiz.com
 */
?>
<?php 
class Html extends CHtml
{
        /**
         * Generates the JavaScript that initiates an AJAX request, and update the target ELEMENT.
         * @param string $target the target need to update, this target must be "Unique" in this DOM.
         * @param string $link the link to trgger this AJAX request, bind on "click" event.
         * @param array $options @see CHtml::ajax($options)
         * @return string the generated JavaScript
         */
        public static function ajaxUpdate($target, $link, $options = array())
        {
            $afterUpdate = isset($options['afterUpdate']) ? $options['afterUpdate'] : '';
            unset($options['afterUpdate']);
            $id = CHtml::ID_PREFIX.self::$count++;
            $ajax = CHtml::ajax(array_merge($options, array(
                'url' => new CJavaScriptExpression("\$(this).attr('href') || location.href"), 
                'success' => new CJavaScriptExpression("function (html){
            var HTML = \$(n, '<div>' + html + '</div>');
            \$(n).html(HTML.length ? HTML.html() : html);
            $afterUpdate;
            AjaxUpdate_{$id}(n,l);}"))));
            return new CJavaScriptExpression("(function AjaxUpdate_{$id}(n, l) {
    \$(n).find(l).click(function (){
        {$ajax} 
        return false;
    })})('{$target}', '{$link}')");
        }
}
