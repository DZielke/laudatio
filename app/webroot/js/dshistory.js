function deleteVersion(pid,dsID,date){
    if(confirm('Are you sure you want to delete this version ?')){
        var myForm = document.createElement("form");
        myForm.method="post";
        var myInput = document.createElement("input");
        myInput.setAttribute("name","pid");
        myInput.setAttribute("value",pid);
        myForm.appendChild(myInput);

        var myInput2 = document.createElement("input");
        myInput2.setAttribute("name","dsID");
        myInput2.setAttribute("value",dsID);
        myForm.appendChild(myInput2);

        var myInput3 = document.createElement("input");
        myInput3.setAttribute("name","date");
        myInput3.setAttribute("value",date);
        myForm.appendChild(myInput3);
        document.body.appendChild(myForm);
        myForm.submit();
        document.body.removeChild(myForm);
    }else{
        return false;
    }
}