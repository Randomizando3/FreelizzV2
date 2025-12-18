function confirmPost(formId,msg){
  if(confirm(msg||'Confirmar?')) document.getElementById(formId).submit();
}
