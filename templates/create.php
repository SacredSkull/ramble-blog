{% extends 'skeleton.php' %}
	{% block title %}
		<h1 class="title" id="theme_name">Back End &#62;</h1>
		<p class="title">{{mode|title}} Post</p>
		<hr>
		<h4></h4>
	{% endblock title %}

	{% block left_nav %}
	{% endblock left_nav %}

	{% block content %}

		<textarea id="postedit" style="display: none;">{{post.getBody}}</textarea>
		<form class="form-inline" role="form" id="form_post" action="/admin/{{post.getId}}" method="POST">
			{%if mode == "edit"%}
			<input type="hidden" name="_METHOD" value="PUT"/>
			{% endif %}
			<div class="input-group">
				<span class="input-group-addon">Title</span>
				<input value="{{post.getTitle}}" id="form_title" type="text" name="title">
			</div>
			<div class="input-group">
				<button class="btn btn-default" id="form_img">
					<span class="glyphicon glyphicon-picture"></span>
				</button>
				<div class="btn-group">
					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="form_other-post">
						<span class="glyphicon glyphicon-share-alt"></span>
					</button>
					<ul id="form_other-post-dropdown" class="dropdown-menu" role="menu">
						<li class="divider"></li>
					</ul>
				</div>
			</div>
				<div name="body" id="epiceditor"></div>
				<!--<input id="save" type="submit" value="Save">-->
				<button id="save" type="submit">
					<span class="glyphicon glyphicon-floppy-save"></span>
				</button>
		</form>
        <form action="/upload/{{post.getId}}" method="POST" enctype='multipart/form-data' style="display: none;">
		  <input type="file" id="form_image">
        </form>
	{% endblock content %}

	{% block right_nav %}
	{% endblock right_nav %}

	{% block additional_js %}
		<script type="text/javascript" src="/include/js/epiceditor.js"></script>
		<script type="text/javascript">
		var opts = {
		  container: 'epiceditor',
		  textarea: 'postedit',
		  basePath: '/include/css/themes',
		  clientSideStorage: true,
		  localStorageName: 'epiceditor',
		  useNativeFullscreen: true,
		  parser: marked,
		  file: {
			name: 'epiceditor',
			defaultContent: '',
			autoSave: 100
		  },
		  theme: {
			base: '/base/epiceditor.css',
			preview: '/preview/preview-dark.css',
			editor: '/editor/epic-dark.css'
		  },
		  button: {
			preview: true,
			fullscreen: true,
			bar: "auto"
		  },
		  focusOnLoad: false,
		  shortcut: {
			modifier: 18,
			fullscreen: 70,
			preview: 80
		  },
		  string: {
			togglePreview: 'Toggle Preview Mode',
			toggleEdit: 'Toggle Edit Mode',
			toggleFullscreen: 'Enter Fullscreen'
		  },
		  autogrow: {
			minHeight: 400,
			maxHeight: 600,
			scroll: true
		  }
		};

		var editor = new EpicEditor(opts).load();

		$(document).ready(function(){

		});

		insertAtCaret = function(element, text) {
			var frag, node, nodeToInsert, range, selection, _window;
			_window = element.ownerDocument.defaultView;
			selection = _window.getSelection();
			if (selection.rangeCount) {
				range = selection.getRangeAt(0);
				range.deleteContents();
				node = document.createTextNode(text);
				frag = document.createDocumentFragment();
				nodeToInsert = frag.appendChild(node);
				return range.insertNode(frag);
			} else {
				return $(element).append(text);
			}
		};
		$("#form_post").on('submit', function(event){
			event.preventDefault();
			return false;
		});

		$( '#form_img' ).on('click', function(){
			$('#form_image').click();
			$('#form_image').change(function(){

                var file_data = $('#form_image').prop('files')[0];   
                var form_data = new FormData();                  
                form_data.append('file', file_data);

				if($('#form_image').val().length > 1){
                    $.ajax({
                        type: "POST",
                        url: "/upload/{{post.getId}}",
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(stuff){
                            JSON.parse(stuff);
                        },
                        error: function(error){
                        	console.log(error);
                            JSON.parse(error);
                        }
                    });

					var container, txtToAdd, altText, titleText;
					altText = prompt("Please enter Alt text; important for google and those who disable images, so describe the image with keywords.");
					titleText = prompt("Please enter the Image title, shows up on roll-over, can be witty, can be smart, can be stupid, but it needs to be relevant.");
					container = $("#epiceditor iframe").contents().find("iframe#epiceditor-editor-frame").contents().find("body").get(0);
					txtToAdd = "!["+ altText + "](https://sacredskull-blog.s3.amazonaws.com/images/test.jpg \""+ titleText +"\")";
					insertAtCaret(container, txtToAdd);
				}
			});
		});

		$('#form_other-post').click(function(){
			$.getJSON('/api/posts/', function(jsonAllPosts){
				$('#form_other-post-dropdown').html('<input type="text"></input>');
				$('#form_other-post-dropdown input').focus();
				/*
				$.each(jsonAllPosts, function(index, value){
					if(value !== null){
						$('#form_other-post-dropdown').append('<li class="other-post-list"><a onclick="insertPostRef()" class="other-post-list-link" href="#">' + "<code>" + value.theme + "</code><kbd>" + value.title + '</kbd></li></a>');
					}
				});
				*/

				console.log(jsonAllPosts);
				var container = $("#epiceditor iframe").contents().find("iframe#epiceditor-editor-frame").contents().find("body").get(0);
				//insertAtCaret(container);
			});
		});

		function asd(){
			alert('lol');
		}

		$( "#save" ).click(function(){
			$("#save span").removeClass('glyphicon-floppy-save');
			$("#save span").addClass('glyphicon-transfer');
			$("#save").css('color', '#7DBEEE');
			console.log('Attempting AJAX...');
			var form_title = $('#form_title').val();
			editor.save();
			var form_body = editor.exportFile();
			$.post( "/admin/{{post.getId}}",
			{
				'body': form_body,
				'title': form_title
			}).done(function(data){
				console.log('Received back ' + data);
				$('#save span').removeClass('glyphicon-transfer');
				$('#save span').addClass('glyphicon-floppy-saved');
				$('#save').css('color', '#00C600');
				if($.isNumeric(data)){
					console.log(data + " seems to be numeric!");
					window.location.href = "/post/" + data;
				}
				console.log('Not a number!');
			}).fail(function(){
				$("#save span").removeClass('glyphicon-transfer');
				$("#save span").addClass('glyphicon-remove');
				$('#save').css('color', '#BD0909');
				console.log('Failed!');
			});
		});
		</script>
	{% endblock additional_js %}
