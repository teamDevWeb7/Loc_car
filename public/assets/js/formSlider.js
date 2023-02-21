function slider(formTarget){

    let connectionForm=document.getElementById('FormConn');
    let inscriptionForm=document.getElementById('FormIns');
    switch(formTarget){
        case 'inscription':
            connectionForm.setAttribute('style', 'transform:translateX(-110%)');
            inscriptionForm.setAttribute('style', 'transform:translateX(-110%)');
            break;


        case 'connection':
            inscriptionForm.setAttribute('style', 'transform:translateX(30%)');
            connectionForm.setAttribute('style', 'transform:translateX(0)');
            break;
    }
}