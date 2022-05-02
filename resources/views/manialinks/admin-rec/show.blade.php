<? xml version = "1.0" encoding = "utf-8" ?>
<manialink>
    <timeout>0</timeout>

    <label text="Cheater's login:" posn="-2 6 0" halign="right"/>
    <entry posn="0 6 0" sizen="15 3" name="login" default="{{ $login }}" halign="left"/>

    <label text="Admin Password:" posn="-2 0 0" halign="right"/>
    <entry posn="0 0 0" sizen="15 3" halign="left" name="password"/>

    <label text="Reset" posn="-64 -45 0" manialink="{{ route('admin_rec_show') }}" style="TextCardRaceRank"/>

    <label text="Delete record and ban player" posn="0 -6 0"
           manialink="{{ route('admin_rec_delete_and_ban') }}?uid={{ $uid }}&amp;login=login&amp;password=password"
           style="CardButtonMediumWide" halign="center"/>

    @if(isset($message))
        <label text="{{ $message }}" posn="0 13 0" halign="center"/>
    @endif

</manialink>
