/*
* jQuery UI Tag-it!
*
* @version v2.0 (06/2011)
*
* Copyright 2011, Levy Carneiro Jr.
* Released under the MIT license.
* http://aehlke.github.com/tag-it/LICENSE
*
* Homepage:
*   http://aehlke.github.com/tag-it/
*
* Authors:
*   Levy Carneiro Jr.
*   Martin Rehfeld
*   Tobias Schmidt
*   Skylar Challand
*   Alex Ehlke
*
* Maintainer:
*   Alex Ehlke - Twitter: @aehlke
*
* Dependencies:
*   jQuery v1.4+
*   jQuery UI v1.8+
*/
(function($) {

    $.widget('ui.tagit', {
        options: {
            allowDuplicates   : false,
            caseSensitive     : true,
            fieldName         : 'tags',
            placeholderText   : null,   // Sets `placeholder` attr on input field.
            readOnly          : false,  // Disables editing.
            removeConfirmation: false,  // Require confirmation to remove tags.
            tagLimit          : null,   // Max number of tags allowed (null for unlimited).

            // Use to override or add any options to the autocomplete widget.
            //
            // By default, autocomplete.source will map to availableTags,
            // unless overridden.
            autocomplete: {},

            // Shows autocomplete before the user even types anything.
            showAutocompleteOnFocus: false,

            // When enabled, quotes are unneccesary for inputting multi-word tags.
            allowSpaces: false,

            // The below options are for using a single field instead of several
            // for our form values.
            //
            // When enabled, will use a single hidden field for the form,
            // rather than one per tag. It will delimit tags in the field
            // with singleFieldDelimiter.
            //
            // The easiest way to use singleField is to just instantiate tag-it
            // on an INPUT element, in which case singleField is automatically
            // set to true, and singleFieldNode is set to that element. This
            // way, you don't need to fiddle with these options.
            singleField: true,


            // Set this to an input DOM node to use an existing form field.
            // Any text in it will be erased on init. But it will be
            // populated with the text of tags as they are created,
            // delimited by singleFieldDelimiter.
            //
            // If this is not set, we create an input node for it,
            // with the name given in settings.fieldName.
            singleFieldNode: null,

            // used in conjuction with singleFieldNode
            // if true assume the input field value is a JSON string representing an
            // array with label and value. Example:
            //      [{ label: 'First Tag', value: 1}, { label: 'Second tag', value: 2 }]
            // This is useful for storing extra data with the tags for many-to-many relationships
            // on the backend

            singleFieldNodeJsonData: false,

            // Whether to animate tag removals or not.
            animate: true,

            // Optionally set a tabindex attribute on the input that gets
            // created for tag-it.
            tabIndex: null,

            // Event callbacks.
            beforeTagAdded      : null,
            afterTagAdded       : null,

            beforeTagRemoved    : null,
            afterTagRemoved     : null,

            onTagClicked        : null,
            onTagLimitExceeded  : null

        },

        _create: function() {
            console.log("__intitialization ...");

            var isFirstRun = true;
            // for handling static scoping inside callbacks
            var that = this;

            // There are 2 kinds of DOM nodes this widget can be instantiated on:
            //     1. UL, OL, or some element containing either of these.
            //     2. INPUT, in which case 'singleField' is overridden to true,
            //        a UL is created and the INPUT is hidden.
            if (this.element.is('input')) {
                this.tagList = $('<ul></ul>').insertAfter(this.element);
                this.options.singleField = true;
                this.options.singleFieldNode = this.element;
                this.element.css('display', 'none');
            } else {
                this.tagList = this.element.find('ul, ol').andSelf().last();
            }

            this.tagInput = $('<input type="text" />').addClass('ui-widget-content');

            if (this.options.readOnly) this.tagInput.attr('disabled', 'disabled');

            if (this.options.tabIndex) {
                this.tagInput.attr('tabindex', this.options.tabIndex);
            }

            if (this.options.placeholderText) {
                this.tagInput.attr('placeholder', this.options.placeholderText);
            }

            if (this.options.showAutocompleteOnFocus) {
                this.tagInput.focus(function(event, ui) {
                    that._showAutocomplete();
                });

                if (typeof this.options.autocomplete.minLength === 'undefined') {
                    this.options.autocomplete.minLength = 0;
                }
            }

            // Bind autocomplete.source callback functions to this context.
            if ($.isFunction(this.options.autocomplete.source)) {
                this.options.autocomplete.source = $.proxy(this.options.autocomplete.source, this);
            }

            this.tagList
                .addClass('tagit')
                .addClass('ui-widget ui-widget-content ui-corner-all')
                // Create the input field.
                .append($('<li class="tagit-new"></li>').append(this.tagInput))
                .click(function(e) {
                    var target = $(e.target);
                    if (target.hasClass('tagit-label')) {
                        var tag = target.closest('.tagit-choice');
                        if (!tag.hasClass('removed')) {
                            that._trigger('onTagClicked', e, {tag: tag, tagLabel: that.tagLabel(tag)});
                        }
                    } else {
                        // Sets the focus() to the input field, if the user
                        // clicks anywhere inside the UL. This is needed
                        // because the input field needs to be of a small size.
                        that.tagInput.focus();
                    }
                });

            // Single field support.
            var addedExistingFromSingleFieldNode = false;
            if (this.options.singleField) {
                var node;

                // this will build the tags from a JSON array stored in the input value field
                node = $(this.options.singleFieldNode);
                var items = JSON.parse(node.val());
                $.each(items, function(index, item) {
                    that.createTag(item, null, true);
                    addedExistingFromSingleFieldNode = true;
                });
            }

            // Add existing tags from the list, if any.
            if (!addedExistingFromSingleFieldNode) {
                this.tagList.children('li').each(function() {
                    if (!$(this).hasClass('tagit-new')) {
                        that.createTag($(this).text(), $(this).attr('class'), true);
                        $(this).remove();
                    }
                });
            }

            // Events.
            this.tagInput
                .keydown(function(event) {
                    // Backspace is not detected within a keypress, so it must use keydown.
                    if (event.which == $.ui.keyCode.BACKSPACE && that.tagInput.val() === '') {
                        var tag = that._lastTag();
                        if (!that.options.removeConfirmation || tag.hasClass('remove')) {
                            // When backspace is pressed, the last tag is deleted.
                            that.removeTag(tag);
                        } else if (that.options.removeConfirmation) {
                            tag.addClass('remove ui-state-highlight');
                        }
                    } else if (that.options.removeConfirmation) {
                        that._lastTag().removeClass('remove ui-state-highlight');
                    }

                    // Comma/Space/Enter are all valid delimiters for new tags,
                    // except when there is an open quote or if setting allowSpaces = true.
                    // Tab will also create a tag, unless the tag input is empty,
                    // in which case it isn't caught.
                    if (
                        event.which === $.ui.keyCode.COMMA ||
                        event.which === $.ui.keyCode.ENTER ||
                        (
                            event.which == $.ui.keyCode.TAB &&
                            that.tagInput.val() !== ''
                        ) ||
                        (
                            event.which == $.ui.keyCode.SPACE &&
                            that.options.allowSpaces !== true &&
                            (
                                $.trim(that.tagInput.val()).replace( /^s*/, '' ).charAt(0) != '"' ||
                                (
                                    $.trim(that.tagInput.val()).charAt(0) == '"' &&
                                    $.trim(that.tagInput.val()).charAt($.trim(that.tagInput.val()).length - 1) == '"' &&
                                    $.trim(that.tagInput.val()).length - 1 !== 0
                                )
                            )
                        )
                    ) {
                        // Enter submits the form if there's no text in the input.
                        if (!(event.which === $.ui.keyCode.ENTER && that.tagInput.val() === '')) {
                            event.preventDefault();
                        }

                        // Autocomplete will create its own tag from a selection and close automatically.
                        if (!that.tagInput.data('autocomplete-open')) {
                            that.createTag(that._cleanedInput());
                        }

                    }
                }).blur(function(e){
                    // Create a tag when the element loses focus.
                    // If autocomplete is enabled and suggestion was clicked, don't add it.
                    if (!that.tagInput.data('autocomplete-open')) {
                        that.createTag(that._cleanedInput());
                    }
                });

            // Autocomplete.
            if (this.options.autocomplete.source) {
                var autocompleteOptions = {
                    select: function(event, ui) {

                        that.createTag(ui.item);

                        // Preventing the tag input to be updated with the chosen value.
                        return false;
                    },
                    focus: function(event, ui) {
                        console.log("running focus ...");
//                        debugger;
                        that.tagInput.val(ui.item.label);
                        return false;
                    }
                };
                $.extend(autocompleteOptions, this.options.autocomplete);

                this.tagInput.autocomplete(autocompleteOptions).bind('autocompleteopen', function(event, ui) {
                    that.tagInput.data('autocomplete-open', true);
                }).bind('autocompleteclose', function(event, ui) {
                    that.tagInput.data('autocomplete-open', false)
                });
            }

            console.log("__ end initialization");

            isFirstRun = false;

        },

        _cleanedInput: function() {
            // Returns the contents of the tag input, cleaned and ready to be passed to createTag
            return $.trim(this.tagInput.val().replace(/^"(.*)"$/, '$1'));
        },

        _lastTag: function() {
            return this.tagList.find('.tagit-choice:last:not(.removed)');
        },

        _tags: function() {
            return this.tagList.find('.tagit-choice:not(.removed)');
        },

        assignedTags: function() {
            // Returns an array of tag string values
            var that = this;
            var tags = [];

            if ($(this.options.singleFieldNode).val() == '') {
                tags = []
            } else {
                tags = JSON.parse(this.options.singleFieldNode.val());
            }

            return tags;
        },

        _updateSingleTagsField: function(tags) {
            if (this.options.singleFieldNodeJsonData) {
                var jsonString = JSON.stringify(tags);
                console.log("tags.toString() = " + jsonString);
                $(this.options.singleFieldNode).val(jsonString);
            } else {
                // Takes a list of tag string values, updates this.options.singleFieldNode.val to the tags delimited by this.options.singleFieldDelimiter
                $(this.options.singleFieldNode).val(tags.join(this.options.singleFieldDelimiter)).trigger('change');
            }
        },

        _subtractArray: function(a1, a2) {
            var result = [];
            for (var i = 0; i < a1.length; i++) {
                if ($.inArray(a1[i], a2) == -1) {
                    result.push(a1[i]);
                }
            }
            return result;
        },

        tagLabel: function(tag) {
            // Returns the tag's string label.
            if (this.options.singleField) {
                return $(tag).find('.tagit-label:first').text();
            } else {
                return $(tag).find('input:first').val();
            }
        },

        _showAutocomplete: function() {
            this.tagInput.autocomplete('search', '');
        },

        _findTagByLabel: function(name) {
            var that = this;
            var tag = null;
            this._tags().each(function(i) {
                if (that._formatStr(name) == that._formatStr(that.tagLabel(this))) {
                    tag = $(this);
                    return false;
                }
            });
            return tag;
        },

        _findTagByValue: function(valueStr) {
            var that = this;
            var tag = null;

            this._tags().each(function(i) {
                if (valueStr == $(this).attr("data-value")) {
                    tag = $(this);
                    return false;
                }
            });

            return tag;
        },

        _isNew: function(name) {
            return !this._findTagByValue(name.value);
        },

        _formatStr: function(str) {
            if (this.options.caseSensitive) {
                return str;
            }
            return $.trim(str.toLowerCase());
        },

        _effectExists: function(name) {
            return Boolean($.effects && ($.effects[name] || ($.effects.effect && $.effects.effect[name])));
        },

        createTag: function(item, additionalClass, duringInitialization) {
            if (item.value == undefined) {
                return false;
            }

            console.log("value " + JSON.stringify(item));
            var that = this;

            if(this.options.preprocessTag) {
                item = this.options.preprocessTag(item);
            }

            if (item === '') {
                return false;
            }

            if (!this.options.allowDuplicates && !this._isNew(item)) {
//                var existingTag = this._findTagByLabel(value);
                console.log("valueStr: " + item.value);
                var existingTag = this._findTagByValue(item.value);
                if (this._trigger('onTagExists', null, {
                    existingTag: existingTag,
                    duringInitialization: duringInitialization
                }) !== false) {
                    if (this._effectExists('highlight')) {
                        existingTag.effect('highlight');
                    }
                }
                return false;
            }

            if (this.options.tagLimit && this._tags().length >= this.options.tagLimit) {
                this._trigger('onTagLimitExceeded', null, {duringInitialization: duringInitialization});
                return false;
            }

            var label;

            // build tag content
            console.debug("label: " + JSON.stringify(item));
            label = $(this.options.onTagClicked ? '<a class="tagit-label"></a>' : '<span class="tagit-label"></span>').text(item.label);

            // Create tag.
            var tag = $('<li></li>')
                .addClass('tagit-choice ui-widget-content ui-state-default ui-corner-all')
                .addClass(additionalClass)
                .append(label);

            // add some extra info to the <li> elments
            tag.attr('data-value', item.value);

            if (this.options.readOnly){
                tag.addClass('tagit-choice-read-only');
            } else {
                tag.addClass('tagit-choice-editable');
                // Button for removing the tag.
                var removeTagIcon = $('<span></span>')
                    .addClass('ui-icon ui-icon-close');
                var removeTag = $('<a><span class="text-icon">\xd7</span></a>') // \xd7 is an X
                    .addClass('tagit-close')
                    .append(removeTagIcon)
                    .click(function(e) {
                        // Removes a tag when the little 'x' is clicked.
                        that.removeTag(tag);
                    });
                tag.append(removeTag);
            }


            if (this._trigger('beforeTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            }) === false) {
                return;
            }

            if (!duringInitialization) {
                var tags = this.assignedTags();

                tags.push(item);
                this._updateSingleTagsField(tags);
            }
            this.tagInput.val('');

            // Insert tag.
            this.tagInput.parent().before(tag);

            this._trigger('afterTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            });

            if (this.options.showAutocompleteOnFocus && !duringInitialization) {
                setTimeout(function () { that._showAutocomplete(); }, 0);
            }
        },

        removeTag: function(tag, animate) {
            animate = typeof animate === 'undefined' ? this.options.animate : animate;

            tag = $(tag);

            if (this._trigger('beforeTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)}) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();

                var elementIndex = tag.index();

                tags.splice(elementIndex, 1);

                this._updateSingleTagsField(tags);
            }

            if (animate) {
                tag.addClass('removed'); // Excludes this tag from _tags.
                var hide_args = this._effectExists('blind') ? ['blind', {direction: 'horizontal'}, 'fast'] : ['fast'];

                var thisTag = this;
                hide_args.push(function() {
                    tag.remove();
                    thisTag._trigger('afterTagRemoved', null, {tag: tag, tagLabel: thisTag.tagLabel(tag)});
                });

                tag.fadeOut('fast').hide.apply(tag, hide_args).dequeue();
            } else {
                tag.remove();
                this._trigger('afterTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)});
            }

        },

        removeTagByLabel: function(tagLabel, animate) {
            var toRemove = this._findTagByLabel(tagLabel);
            if (!toRemove) {
                throw "No such tag exists with the name '" + tagLabel + "'";
            }
            this.removeTag(toRemove, animate);
        },

        removeAll: function() {
            // Removes all tags.
            var that = this;
            this._tags().each(function(index, tag) {
                that.removeTag(tag, false);
            });
        }

    });
})(jQuery);

