var isMeetingRunning={isMeetingRunning};
var isMaxNumberOfSessionsExceeded = {isMaxNumberOfSessionsExceeded};
if (isMaxNumberOfSessionsExceeded){
    $('#openClassLink').hide();
    $('#classNotStarted').hide();
    $('#maxNumberofSessionsExceeded').show();
}else if(isMeetingRunning){
    $('#openClassLink').show();
    $('#classNotStarted').hide();
}else{
    $('#openClassLink').hide();
    $('#classNotStarted').show();
}

var isMeetingRecorded={isMeetingRecorded};
if(isMeetingRecorded){
    $('#isMeetingRecorded').show();
}else{
    $('#isMeetingRecorded').hide();
}
var hasMeetingRecordings = !!'{hasMeetingRecordings}';

<!-- BEGIN moderator -->
function deleteRecord (link){

    $('#recordID').val(link);
    $('#deleteRecordingInput').click();

}
var dummyVar={DUMMY_VAL};
$('#endClassForm').hide();
$('#startClassForm').hide();
$('#deleteRecording').hide();
$('#maxNumberofSessionsExceeded').hide();

if(isMeetingRunning){
    if(isMaxNumberOfSessionsExceeded){
        $('#maxNumberofSessionsExceeded').show();
    }
    $('#openClassLink').show();
    $('#endClassDiv').show();
    $('#startClassDiv').hide();
    $('#recordmeeting_visible').hide();
    $('#recordmeeting_label').hide();

}else{
    $('#openClassLink').hide();
    $('#endClassDiv').hide();
    $('#startClassDiv').show();
    console.log($('#endClassDiv'))
}


if(hasMeetingRecordings){
    $('#recordings').show();
}
else{
    $('#recordings').hide();
}


if(isMeetingRecorded){
    $('#isMeetingRecorded').show();
}else{
    $('#isMeetingRecorded').hide();
}

$('#startClassLink').click(function() {
    $('#recordmeeting').prop("checked",$('#recordmeeting_visible').prop("checked"));
    //alert($('#recordmeeting_visible').prop("checked"));
    $('#startClassFormInput').click();

    ////$('#openClassLink').click();
});

$('#endClassLink').click(function() {
    $('#endClassFormInput').click();
    //$('#openClassLink').click();
});
$('#copyUserInviteUrl', document).on('click', function(e) {

    let copyText = $('#userInviteUrl', document).get(0);
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/
    document.execCommand("copy");
});

$('#copyGuestLinkPw', document).on('click', function(e) {

    let copyPw = $('#guestLinkPw', document).get(0);
    copyPw.select();
    copyPw.setSelectionRange(0, 99999); /*For mobile devices*/
    document.execCommand("copy");
});

<!-- END moderator -->