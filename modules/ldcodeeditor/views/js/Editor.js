/*
 * LDCE
 * https://leodesigaux.com/
 *
 * Copyright JS Foundation and other contributors
 * Released under the MIT license
 * https://leodesigaux.com/
 *
 * Date: 2020-05-04T22:49Z
 */
var Editor = function (res) {
    this.uid = res.uid;
    this.path = res.path;
    this.id = res.id;
    this.dirty = false;
    this.active = false;
    this.content = res.content;
    this.filename = res.filename;
    this.type = res.type;
    this.editorTypes = {
      'scss': 'text/x-sass',
      'css': 'text/css',
      'html': 'text/html',
      'twig': 'application/x-httpd-php',
      'php': 'application/x-httpd-php',
      'tpl': 'application/x-httpd-php',
      'less': 'text/css',
      'json': 'text/javascript',
      'js': 'text/javascript',
      'map': 'text/javascript',
      'xml': 'application/xml',
      'svg': 'application/xml'
    };
      this.createTab = function () {
        $(".ldce_bar").append('<div class="ldce_tab" data-id="' + this.uid + '"><span>' + this.filename + '</span><span class="close"></span></div>');
        LDCE.setActive(this.uid)
      };
      this.destroy = function () {
        $('.ldce_tab[data-id="' + this.uid + '"]').remove();
        $('.ldce_editor_instance[data-id="' + this.uid + '"]').remove();
      };
      this.createEditor = function () {
        $('.ldce_editor_cm').append('<div class="ldce_editor_instance" data-id="' + this.uid + '"></div>')
        var newEditor = new CodeMirror($('.ldce_editor_instance[data-id="' + this.uid + '"]')[0], {
          lineNumbers: true,
          theme: LDCE.getTheme(),
          value: this.content
        });
        newEditor.setValue(this.content);
        newEditor.refresh();
        newEditor.setOption("mode", this.editorTypes[this.type]);
        newEditor.uid = this.uid;
        $('.ldce_editor').css({
          'width':  'calc(100% - '+parseInt($(".ldce_spliter").css('left'))+'px)'
        })
        newEditor.on('change', function(cm){
          this.setDirty();
        }.bind(this))
        this.createTab();
        return newEditor;
      };
      this.rename = function (newUid, newPath, newId) {
        console.log(3);
        $(".ldce_tab[data-id='" + this.uid + "']").attr('data-id', newUid)
        $(".ldce_editor_instance[data-id='" + this.uid + "']").attr('data-id', newUid)
        this.uid = newUid;
        this.editor.uid = newUid;
        this.path = newPath;
        this.id = newId;
      };
      this.setActive = function (isActive, shake) {
        var wasActive = this.active;
        this.active = isActive;
        if(isActive) {
          $(".ldce_tab[data-id='" + this.uid + "']").addClass('active')
          $(".ldce_editor_instance[data-id='" + this.uid + "']").show()
          if(wasActive && shake){
            $(".ldce_tab[data-id='" + this.uid + "']").animate({ left: 15 * -1 }, (400 / 2 / 4))
            .animate({ left: 15 }, (400 / 2 / 2))
            .animate({ left: 0 }, (400 / 2 / 4));
            $(".ldce_tab[data-id='" + this.uid + "']").animate({ left: 15 * -1 }, (400 / 2 / 4))
            .animate({ left: 15 }, (400 / 2 / 2))
            .animate({ left: 0 }, (400 / 2 / 4));
          }
        } else {
          $(".ldce_tab[data-id='" + this.uid + "']").removeClass('active')
          $(".ldce_editor_instance[data-id='" + this.uid + "']").hide()
        }
      };
      this.setDirty = function () {
        this.dirty = true;
        $(".ldce_tab[data-id='" + this.uid + "']").addClass('unsaved')
        LDCE.verifyDirty();
      };
      this.setClean = function () {
        this.dirty = false;
        $(".ldce_tab[data-id='" + this.uid + "']").removeClass('unsaved')
        LDCE.verifyDirty();
      };
      this.editor = this.createEditor();
}