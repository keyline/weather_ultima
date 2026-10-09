@props(['url'])
@php ($siteSettings = \App\Models\SiteSetting::current())
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ $siteSettings->header_logo_url }}" alt="{{ $siteSettings->display_name }}" style="height: auto; max-height: 90px; max-width: 220px; width: auto;">
</a>
</td>
</tr>
