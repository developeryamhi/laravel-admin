@if(Session::has(FLASH_MSG_INFO))
{{ infoAlertMsg(Session::get(FLASH_MSG_INFO), true) }}
@endif

@if(Session::has(FLASH_MSG_SUCCESS))
{{ successAlertMsg(Session::get(FLASH_MSG_SUCCESS), true) }}
@endif

@if(Session::has(FLASH_MSG_ERROR))
{{ errorAlertMsg(Session::get(FLASH_MSG_ERROR), true) }}
@endif

@if(Session::has(FLASH_MSG_WARNING))
{{ warningAlertMsg(Session::get(FLASH_MSG_WARNING), true) }}
@endif