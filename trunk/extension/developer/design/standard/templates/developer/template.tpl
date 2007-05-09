<h1>Template code parser</h1>

<form action={"/developer/template"|ezurl} method="post">
<textarea name="TemplateCodeInput" style="width:99%;" rows="20">{$template_code|wash}</textarea>
<input type="submit" name="ParseButton" value="Parse" />
</form>

{if is_set( $parsed_code )}
<h2>Output</h2>
<pre>
{$parsed_code|wash}
</pre>
{/if}