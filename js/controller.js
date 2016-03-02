"use strict";
(function(){
    /* Contenedores */
    var textarea = document.getElementById('help-panel');
    var listaDiv = document.getElementById('lista');
    var entriesTable = new EntriesTable();
    /* Paginacion */
    var page = 1; //Pagina actual
    var pages = 1; //Numero de pagina
    var nrpp = 10;
    /* Objeto para peticiones Ajax */
    function ajaxRequest(method, url, success, error, asinc, data){
        this.method = method;
        this.data = data;
        this.url = url;
        this.success = success;
        this.error = error;
        this.asinc = asinc;
        this.sendRequest = function(){
            var xhttp = new XMLHttpRequest();
            xhttp.open(this.method, this.url, this.asinc);
            if(this.method == "POST"){
                xhttp.setRequestHeader("Content-type", "multpart/form-data");
                xhttp.send(this.data);
            }else
                xhttp.send();
            xhttp.onreadystatechange = function(){
                if(xhttp.readyState == 4){
                    if(xhttp.status == 200){
                        success(xhttp);
                    }else{
                        error(xhttp);
                    }
                }    
            }
        };
    };
    /* Funciones utiles */
    //Limpia el contenido de un nodo
    function clearNode(node){
        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }
    }
    function removeEle(ele){
        ele.parentNode.removeChild(ele);
    }
    //Maneja un error en ajax
    function doError(xhttp){
        textarea.textContent += 'ReadyState: '+xhttp.status+' Status: '+xhttp.readyState+'\n';
    };
    //Crea un queryString de un objeto
    function objToQuery(obj){
        var prop;
        var str = ""
        for(prop in obj){
            str += prop+'='+obj[prop]+'&'; 
        }
        return str.substr(0, str.length-1);
    }
    function commintProps(tmp, obj){
        var prop;
        for(prop in obj){
            if(tmp[prop])
                obj[prop] = tmp[prop];
        }
    }
    function readForm(obj, formu){
        var prop, value;
        for(prop in obj){
            value = formu.getElementById(prop).value;
            if(value)
                obj[prop] = value;
        }
    }
    /* Objetos de vista */
    //Objeto post maneja las entradas del usuario
    function EntriesTable() {
        this.tbody = document.getElementById('entryTableBody');
        this.trs = [];
        this.render = function(){
            for(var i=0;i<this.trs.length;i++){
                this.trs[i].loadData();
                this.tbody.appendChild(this.trs[i].tr);
            }
        };
    }
    function Post(id, title, content, image){
        this.id = id;
        this.title = title;
        this.content = content;
        this.image = image;
    }
    Post.prototype.genDivElem = function(){
        this.dataTable = document.createElement('div');
        this.divElem.setAttribute('id', 'post_'+this.id);
    };
    Post.prototype.setDivElemInlist = function(){
        this.divElem.className = "post-in-list";
    };
    Post.prototype.setDivElemSingle = function(){
        this.divElem.className = "post-single";
    };
    Post.prototype.getPost = function(){
        var that = this;
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                var tmp = response.resultset;
                commintProps(tmp, that);
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=entry&op=read&pkid='+that.id, onAjaxSuccess, doError, true, null);
        ajax.sendRequest();
    }
    Post.prototype.setPost = function(){
        var that = this;
        var tmp = new Post();
        tmp.readForm();
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                commintProps(tmp, that);
            }else{
                textarea.textContent += 'Error updating';
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=entry&op=set&'+objToQuery(tmp), onAjaxSuccess, doError, true, null); //Mira los datos con objeto FormData()
        ajax.sendRequest();
    }
    Post.prototype.deletePost = function(){
        var that = this;
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                that.divElem.parentNode.removeChild(that.divElem);
                that = null;
            }else{
                textarea.textContent += 'delete error';
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=entry&op=delete&pkid='+that.id, onAjaxSuccess, doError, true, null);
        ajax.sendRequest();
    }
    Post.prototype.readForm = function(){
        readForm(this, document.getElementById("entryForm"));
    }
    Post.prototype.loadData = function(){
        this.tr = document.createElement('tr');
        this.tr.setAttribute('id', 'e_'+this.id);
        this.tr.innerHTML='<td>'+this.id+'</td><td>'+this.title+'</td><td>'+this.content+'</td><td>'+this.image+'</td><td><button value="'+this.id+'" class="btn btn-xs btn-warning updateE">Update</button><button value="'+this.id+'" class="btn btn-xs btn-danger deleteE">Delete</button></td>';
    }
    Post.prototype.getPage = function(page, nrpp){
        var that = this;
        var onSucces = function(xHttp){
            var response = JSON.parse(xHttp.responseText);
            var i;
            var data;
            var entries = [];
            for(i=0;i<response.resultset.length;i++){
                data = response.resultset[i];
                entriesTable.trs.push(new Post(data.id, data.title, data.content, data.image));
            }
            entriesTable.render();
            $('.deleteE').on('click', function(ev) {
            var id = ev.target.value;
             var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.result != -1){
                    removeEle(document.getElementById('e_'+id));
                }
            };
            var ajax = new ajaxRequest('GET', '?table=entry&op=delete&pkid='+id, onSucces, onError, true, null);
            ajax.sendRequest();
        });
        };
        var ajax = new ajaxRequest('GET', '?table=entry&op=read&page='+page+'&nrpp='+nrpp, onSucces, onError, true, null);
        ajax.sendRequest();
    }
    //Objeto user maneja las operaciones de usuario
    function User(email, pass, alias, fechaAlta, activo, administrador, personal) {
        this.email = email;
        this.pass = pass;
        this.alias = alias;
        this.fechaAlta = fechaAlta;
        this.activo = activo;
        this.administrador = administrador;
        this.personal = personal;
    }
    User.prototype.getUser = function(){
        var that = this;
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                var tmp = response.resultset;
                commintProps(tmp, that);
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=user&op=get&pkid='+that.email, onAjaxSuccess, doError, true, null);
        ajax.sendRequest();
    }
    User.prototype.setUser = function(){
        var that = this;
        var tmp = new User();
        tmp.readForm();
        var imgUser = $("#imgUser");
        var files = imgUser.files;
        var params = new FormData();
        var file, i;
        for (i = 0; i < files.length; i++) {
            file = files[i];
            params.append('image[]', file, file.name);
        }
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                commintProps(tmp, that);
                that.img = response.resultset.image;
            }else{
                textarea.textContent += 'Error updating';
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=user&op=set', onAjaxSuccess, doError, true, null);
        ajax.sendRequest(params);
    }
    User.prototype.deleteUser = function(){
        var that = this;
        var onAjaxSuccess = function(xhttp){
            var response = JSON.parse(xhttp.responseText);
            if(response.result > 0){
                that = null;
            }else{
                textarea.textContent += 'delete error';
            }
        }
        var ajax = new ajaxRequest('GET', '../index.php?table=user&op=delete&pkid='+that.id, onAjaxSuccess, doError, true, null);
        ajax.sendRequest();
    }
    User.prototype.readForm = function(){
        readForm(this, document.getElementById("userForm"));
    }
    
    /* Manejadores de los botones */
    var btnLogin = $('#btnLogin');
    var btnRegister = $('#btnRegister');
    var btnsingout = $('#btnsingout');
    var btnrecovery = $('#btnrecovery');
    var btnSelectTpl = $('.selectTpl');
    var btnSaveTpl = $('#saveTpl');
    var saveEntry = $('#saveEntry');
    var deleteE = $('.deleteE');
    if(btnLogin && btnRegister){
        var onError = function(xHttp){
            alert('Error');
        };
        btnLogin.on('click', function(ev){
            ev.preventDefault();
            var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.type == 'error'){
                    $('#demo').text('Usuario o contraseña incorectos');
                }else{
                    window.location = "index.php?op=view&view=frontend&userAlias="+$("#login").val(); 
                }
            };
            var data = "login="+$("#login").val()+"&pass1="+$("#pass1").val();
            var ajax = new ajaxRequest('POST', '?table=user&op=login', onSucces, onError, true, data);
            ajax.sendRequest();
        });
        btnRegister.on('click', function(ev){
            ev.preventDefault();
            var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.type == 'error'){
                    $('#demo').text('Usuario o contraseña incorectos');
                }else{
                    $('#demo').text('Registro realizado revise su correo para activar su cuenta');
                }
            };
            var data = "mail="+$("#mail").val()+"&pass1="+$("#pass1r").val()+"&pass2="+$("#pass2r").val();
            var ajax = new ajaxRequest('POST', '?table=user&op=register', onSucces, onError, true, data);
            ajax.sendRequest();
        });
    }
    if(btnsingout){
        btnsingout.on('click', function(ev){
            ev.preventDefault();
            var onSucces = function(xHttp){
                window.location = "index.php?op=view&view=frontend";
            };
            var ajax = new ajaxRequest('GET', '?table=user&op=logout', onSucces, onError, true, null);
            ajax.sendRequest();
        });
    }
     if(btnrecovery){
        btnrecovery.on('click', function(ev){
            ev.preventDefault();
            var onSucces = function(xHttp){
                $('#demo').text('Correo de recuperación enviado correctamente.');
                $('#forgetpass').modal('hide');
            };
            var ajax = new ajaxRequest('GET', '?table=user&op=recovery&mail='+$('#malirec').val(), onSucces, onError, true, null);
            ajax.sendRequest();
        });
    }
    if(btnSelectTpl){
        btnSelectTpl.on('click', function(ev) {
            var idTpl = ev.target.getAttribute('value');
            var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.result != -1){
                    $('#templatesList > figure').each(function(id, ele){
                        $('#'+ele.id).removeClass('bg-Success');
                        $('#'+ele.id).addClass('bg-danger');
                    });
                    $('#templatesList > figure > figcaption > button').each(function(id, ele){
                        $('#'+ele.id).removeClass('btn-Success');
                        $('#'+ele.id).addClass('btn-danger');
                    });

                    $('#tpl_'+idTpl).removeClass('bg-danger');
                    $('#tpl_'+idTpl).addClass('bg-success');
                    $('#seltecTpl_'+idTpl).removeClass('btn-danger');
                    $('#seltecTpl_'+idTpl).addClass('btn-success');
                    $('#editTpl').removeClass('hidden');
                    var tplData = response.tpl;
                    var prop;
                    for(prop in tplData){
                        $('#'+prop+'T').val(tplData[prop]);
                    }
                }
            };
            var ajax = new ajaxRequest('GET', '?op=setTemplate&idTemplate='+idTpl, onSucces, onError, true, null);
            ajax.sendRequest();
        });
    }
    if(btnSaveTpl){
        btnSaveTpl.on('click', function(ev) {
            ev.preventDefault();
            var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.result != -1){
                    $('#editTpl').addClass('hidden');
                }
            };
            var tplData = "idTemplate="+$("#idTemplateT").val()+"&title="+$("#titleT").val()+"&heading="+$("#headingT").val()+"&content="+$("#contentT").val()+"&footer="+$("#footerT").val();
            var ajax = new ajaxRequest('GET', '?op=saveTemplate&'+tplData, onSucces, onError, true, null);
            ajax.sendRequest();
        });   
    }
    if(saveEntry){
        Post.prototype.getPage(page, nrpp);
        $('#pageE').val(page);
        $('#pagesE').text(pages);
        var nextPage = function () {
            $('#entryTable tbody tr').each(function(id, ele) {
                clearNode(ele);
            });
            page = Math.min(pages, page++);
            Post.prototype.getPage(page, nrpp);
        };
        var prevPage = function () {
            $('#entryTable tbody tr').each(function(id, ele) {
                clearNode(ele);
            });
            page = Math.max(1, page--);
            Post.prototype.getPage(page, nrpp);
        };
        $("#nextEntryPage").on('click', function(ev) {
            nextPage();
        });
        $("#prevEntryPage").on('click', function(ev) {
            prevPage();
        });
        saveEntry.on('click', function(ev){
            ev.preventDefault();
             var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.result != -1){
                    var doSucces = function(xHttp2){
                        var ent = JSON.parse(xHttp2.responseText).obj;
                        var tr = document.createElement('tr');
                        tr.setAttribute('id', 'e_'+ent.id);
                        tr.innerHTML = '<td>'+ent.id+'</td><td>'+ent.title+'</td><td>'+ent.content+'</td><td>'+ent.image+'</td><td><button value="'+ent.id+'" class="btn btn-xs btn-warning updateE">Update</button><button value="'+ent.id+'" class="btn btn-xs btn-danger deleteE">Delete</button></td>';
                        $('#entryTable tbody').append(tr);   
                        $('#entryForm').reset();
                    }
                    var entData = "&title="+$("#titleE").val()+"&content="+$("#contentE").val()+"&image="+response.filename;
                    var ajax = new ajaxRequest('GET', 'index.php?op=insertEntry'+entData, doSucces, onError, true, null);
                    ajax.sendRequest();
                }
            };
            var file = (document.getElementById('imageE').files)[0];
            var data = new FormData();
            data.append('image', file, file.name);
            var xHttp = new XMLHttpRequest();
            if(xHttp.upload){
                xHttp.open("POST", "index.php?op=uploadFile", true);
                xHttp.onreadystatechange=function(){
                    if(xHttp.readyState==4){
                        if(xHttp.status==200){
                            onSucces(xHttp);
                        }else{
                            onError(xHttp);
                        }
                    }
                }
            };
            xHttp.send(data);
        });
    }
    if(deleteE){
        deleteE.on('click', function(ev) {
            var id = ev.target.value;
             var onSucces = function(xHttp){
                var response = JSON.parse(xHttp.responseText);
                if(response.result != -1){
                    removeEle(document.getElementById('e_'+id));
                }
            };
            var ajax = new ajaxRequest('GET', '?table=entry&op=delete&pkid='+id, onSucces, onError, true, null);
            ajax.sendRequest();
        });
    }
})();
