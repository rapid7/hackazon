/**
 * Created by Nikolay Chervyakov on 19.12.2014.
 */

/**
 * Controls Vulnerable elements
 */
can.Control('VulnBlock', {
    pluginName: 'vulnBlock',
    defaults: {
        link: null
    }
}, {
    init: function () {
        this.link = this.options.link;
        var $vulns = this.element.find('.vulnerability');

        if (!$vulns.length) {
            this.hide();
        } else {
            this.show();
        }
    },

    '{link} click': function (el, ev) {
        ev.preventDefault();
        this.toggleVisibility();
    },

    hide: function () {
        this.element.hide();
        this.link.text('Show vulnerabilities');
    },

    show: function () {
        this.element.show();
        this.link.text('Hide vulnerabilities');
    },

    toggleVisibility: function () {
        if (this.element.is(':visible')) {
            this.hide();
        } else {
            this.show();
        }
    }
});

/**
 * Controls Vulnerable elements
 */
can.Control('ConditionsBlock', {
    pluginName: 'conditionsBlock',
    defaults: {
        link: null
    }
}, {
    init: function () {
        this.link = this.options.link;
        var $conditions = this.element.find('.js-condition-row');

        if (!$conditions.length) {
            this.hide();
        } else {
            this.show();
        }
    },

    '{link} click': function (el, ev) {
        ev.preventDefault();
        this.toggleVisibility();
    },

    hide: function () {
        this.element.hide();
        this.link.text('Show conditions');
    },

    show: function () {
        this.element.show();
        this.link.text('Hide conditions');
    },

    toggleVisibility: function () {
        if (this.element.is(':visible')) {
            this.hide();
        } else {
            this.show();
        }
    }
});

/**
 * Controls Context
 */
var Context = can.Control('Context', {
    pluginName: 'contextControl'
}, {
    init: function () {
        this.fieldsBlock = this.element.find('> .panel-body > .context-fields');
        this.fieldsContainer = this.fieldsBlock.find('.js-fields-container');

        var fields = this.fieldsContainer.children('.js-field-block');
        this.fieldsIdCounter = fields.length;
        fields.contextField();
        this.fieldsContainer.sortable();
        //this.fieldsContainer.disableSelection();

        this.vulnBlock = this.element.find('> .panel-body > .js-vulns-block');
        this.vulnBlock.vulnBlock({link: this.element.find('> .panel-body > .js-show-vulns-link')});
        this.vulnBlock.children('.js-vulnerability-block').vulnerableElement();

        this.childrenContainer = this.element.find('> .panel-body > .js-child-contexts');
        var children = this.childrenContainer.children('.js-context-panel');
        this.childrenIdCounter = children.length;
        this.childrenContainer.sortable();
        //this.childrenContainer.disableSelection();

        this.hasParents = !!this.element.parents('.js-context-panel').length;

        if (!this.hasParents) {
            this.element.find('> .panel-heading .js-remove').remove();
            this.element.find('> .panel-heading .js-position-buttons').remove();
        }
        this.moveUpButton = this.element.find('> .panel-heading .js-move-up');
        this.moveDownButton = this.element.find('> .panel-heading .js-move-down');

        this.checkPositionButtons();
    },

    '> .panel-body > .context-fields .js-add-field click': function (el, ev) {
        ev.preventDefault();
        this.addField();
    },

    '> .panel-body > .context-fields .js-fields-container sortupdate': function () {
        this.calculateFieldOrder();
    },

    '> .panel-body > .js-child-contexts sortupdate': function () {
        this.checkChildrenPositionButtons();
    },

    '> .panel-body > .context-fields .js-field-block removefieldclaim': function (el) {
        el.remove();
        this.calculateFieldOrder();
    },

    '> .panel-heading .js-add-child-context click': function (el, ev) {
        ev.preventDefault();
        this.addContext();
    },

    '> .panel-heading .js-remove click': function (el, ev) {
        ev.preventDefault();
        this.element.remove();
    },

    '> .panel-heading .js-add-field click': function (el, ev) {
        ev.preventDefault();
        this.addField();
    },

    '> .panel-heading .js-move-up click': function (el, ev) {
        ev.preventDefault();
        var prevElement = this.element.prev('.js-context-panel');

        if (!prevElement.length) {
            return;
        }

        this.element.insertBefore(prevElement);
        this.checkPositionButtons();
        prevElement.contextControl('checkPositionButtons');
    },

    '> .panel-heading .js-move-down click': function (el, ev) {
        ev.preventDefault();
        var nextElement = this.element.next('.js-context-panel');

        if (!nextElement.length) {
            return;
        }

        this.element.insertAfter(nextElement);
        this.checkPositionButtons();
        nextElement.contextControl('checkPositionButtons');
    },

    addField: function () {
        var idBase = this.fieldsBlock.data('field-collection-id');
        var name = this.fieldsBlock.data('field-collection-name');
        var id = this.fieldsIdCounter++;
        var template = can.view("#tplField", {id: idBase + '_' + id, name: name + '[' + id + ']', field_index: id});
        this.fieldsContainer.append(template);
        var newFieldId = '#' + idBase + '_' + id;
        var newField = this.fieldsContainer.children(newFieldId);
        newField.contextField();
        newField.find('.js-name-field').focus();

        this.fieldsContainer.sortable();
        //this.fieldsContainer.disableSelection();

        this.calculateFieldOrder();
    },

    addContext: function () {
        var idBase = this.element.data('id') + '_children';
        var name = this.element.data('name') + '[children]';
        var id = this.childrenIdCounter++;
        var template = can.view("#tplContext", {id: idBase + '_' + id, name: name + '[' + id + ']', field_index: id});
        this.childrenContainer.append(template);
        var newContextId = '#' + idBase + '_' + id;
        var contextElement = this.childrenContainer.children(newContextId);
        contextElement.contextControl();
        location.hash = newContextId;
        contextElement.find('.js-name-field').focus();
        this.checkChildrenPositionButtons();
    },

    calculateFieldOrder: function () {
        var order = this.fieldsContainer.children('.js-field-block').map(function (i, f) { return $(f).data('id'); })
            .toArray().join(',');
        this.element.find('> .panel-body > .js-field-order').val(order);
    },

    checkPositionButtons: function () {
        if (!this.hasParents) {
            return;
        }

        if (this.element.prev('.js-context-panel').length) {
            this.moveUpButton.show();
        } else {
            this.moveUpButton.hide();
        }

        if (this.element.next('.js-context-panel').length) {
            this.moveDownButton.show();
        } else {
            this.moveDownButton.hide();
        }
    },

    checkChildrenPositionButtons: function () {
        this.childrenContainer.children('.js-context-panel').each(function () {
            var panel = $(this);
            panel.contextControl('checkPositionButtons')
        });
    }
});

/**
 * Controls Vulnerable elements
 */
can.Control('ContextField', {
    pluginName: 'contextField'
}, {
    init: function () {
        this.vulnBlock = this.element.find('.js-vulns-block');
        this.vulnBlock.vulnBlock({link: this.element.find('> .panel-body > .field-props .js-show-vulns-link')});
        this.vulnBlock.children('.js-vulnerability-block').vulnerableElement();
    },

    '> .panel-body > .field-props .js-remove-field click': function (el, ev) {
        ev.preventDefault();
        this.element.trigger('removefieldclaim');
    }
});

/**
 * Controls Vulnerable elements
 */
var VulnerableElement = can.Control('VulnerableElement', {
    pluginName: 'vulnerableElement'
}, {
    init: function () {
        this.childrenContainer = this.element.find('> .panel-body > .js-child-vulnerability-elements');
        var children = this.childrenContainer.children('.js-vulnerability-block');
        this.childrenIdCounter = children.length;
        children.vulnerableElement();
        this.childrenContainer.sortable();
        //this.childrenContainer.disableSelection();

        this.vulnList = this.element.find('> .panel-body > .js-vulnerability-set .js-vulnerability-list');
        this.conditionBlock = this.element.find('> .panel-body > .vulnerability-conditions-block');
        this.conditionList = this.conditionBlock.find('.js-condition-list');
        var controller = this;
        this.vulnList.children('.js-vulnerability').each(function () {
            controller.enablePlugin($(this));
        });
        //this.vulnIdCounter = this.vulnList.children('.js-vulnerability').length;

        if (this.element.parents('.js-vulnerability-block').length == 0) {
            this.element.find('> .panel-heading .js-remove').remove();
        }

        if (parseInt(this.element.closest('.js-context-panel').data('edit-mode'), 10) == 0) {
            this.element.find('> .panel-heading .js-collapsible-field').collapsibleField({
                emptyValue: 'Click to set block name...'
            });
        }

        this.conditionBlock.conditionsBlock({link: this.element.find('> .panel-heading > .js-show-conditions')})
    },

    '> .panel-body > .js-vulnerability-set .js-remove-vulnerability click': function (el, ev) {
        ev.preventDefault();
        el.closest('.js-vulnerability').remove();
    },

    '> .panel-body > .vulnerability-conditions-block .js-remove-condition click': function (el, ev) {
        ev.preventDefault();
        el.closest('.js-condition-row').remove();
    },

    '> .panel-heading .js-add-child click': function (el, ev) {
        ev.preventDefault();
        this.addChild();
    },

    '> .panel-heading .js-remove click': function (el, ev) {
        ev.preventDefault();
        el.closest('.js-vulnerability-block').remove();
    },

    '> .panel-heading .js-add-vulnerability-selector click': function (el, ev) {
        ev.preventDefault();

        var existingVulns = this.vulnList.children('.js-vulnerability').map(function (i, vulnElem) {
            return $(vulnElem).data('name');
        }).toArray();

        var options = $.map(this.getAllVulns(), function (val) {
            if ($.inArray(val, existingVulns) !== -1) {
                return '';
            }
            return '<li><a href="#" class="js-add-vulnerability" data-vulnerability="' + val + '">' + val + '</a></li>';
        }).join('');
        el.next('.dropdown-menu').first().html(options);
    },

    '> .panel-heading .js-add-vulnerability click': function (el, ev) {
        ev.preventDefault();
        var vulnerability = el.data('vulnerability');

        var idBase = this.element.data('id') + '_vulnerabilitySet';
        var name = this.element.data('name') + '[vulnerabilitySet]';
        var template = can.view("#tplVulnerability_" + vulnerability, {id: idBase, name: name});
        var vulns = this.vulnList.find('.js-vulnerability');
        if (vulns.length) {
            var curVuln = null, greaterVuln = null;
            for (var i = 0; i < vulns.length; i++) {
                curVuln = vulns.eq(i);
                if (curVuln.data('name').toLowerCase() > vulnerability.toLowerCase()) {
                    greaterVuln = curVuln;
                    break;
                }
            }

            if (greaterVuln) {
                greaterVuln.before(template);
            } else {
                this.vulnList.append(template);
            }

        } else {
            this.vulnList.append(template);
        }

        var newVulnElement = this.vulnList.children('#' + idBase + '_vulnerabilities_' + vulnerability);
        this.enablePlugin(newVulnElement);
    },

    '> .panel-heading .js-add-condition-selector click': function (el, ev) {
        ev.preventDefault();

        var existingConditions = this.conditionList.children('.js-condition-row').map(function (i, condElem) {
            return $(condElem).data('name');
        }).toArray();

        var options = $.map(this.getAllConditions(), function (val) {
            if ($.inArray(val, existingConditions) !== -1) {
                return '';
            }
            return '<li><a href="#" class="js-add-condition" data-condition="' + val + '">' + val + '</a></li>';
        }).join('');
        el.next('.dropdown-menu').first().html(options);
    },

    '> .panel-heading .js-add-condition click': function (el, ev) {
        ev.preventDefault();
        var condition = el.data('condition');

        var idBase = this.element.data('id') + '_conditionSet_';
        var name = this.element.data('name') + '[conditionSet]';
        var template = can.view("#tplCondition_" + condition, {id: idBase, name: name});
        var conditions = this.conditionList.find('.js-condition-row');
        if (conditions.length) {
            var curCond = null, greaterCondition = null;
            for (var i = 0; i < conditions.length; i++) {
                curCond = conditions.eq(i);
                if (curCond.data('name').toLowerCase() > condition.toLowerCase()) {
                    greaterCondition = curCond;
                    break;
                }
            }

            if (greaterCondition) {
                greaterCondition.before(template);
            } else {
                this.conditionList.append(template);
            }

        } else {
            this.conditionList.append(template);
        }

        this.conditionBlock.conditionsBlock('show');
    },

    addChild: function () {
        var idBase = this.element.data('id') + '_children';
        var name = this.element.data('name') + '[children]';
        var id = this.childrenIdCounter++;
        var template = can.view("#tplVulnerableElement", {id: idBase + '_' + id, name: name + '[' + id + ']'});
        this.childrenContainer.append(template);
        console.log('#' + idBase + '_' + id);
        this.childrenContainer.children('#' + idBase + '_' + id).vulnerableElement();
    },

    getVulnInfo: function () {
        return window.VulnInfo || {};
    },

    getAllVulns: function () {
        return (this.element.closest('.js-field-block').length > 0 ? this.getVulnInfo().fieldVulns : this.getVulnInfo().vulns) || [];
    },

    getAllConditions: function () {
        return this.getVulnInfo().conditions || [];
    },

    enablePlugin: function (el) {
        var pluginName = el.data('controller-plugin');
        if (pluginName && jQuery.fn[pluginName]) {
            el[pluginName]();
        }
    }
});

can.Control('IntegerOverflowVulnerability', {
    pluginName: 'integerOverflowVulnerability'
}, {
    init: function () {
        this.attrsElement = this.element.find('.js-vuln-attrs');
        this.enableCheckbox = this.element.find('.js-enable-vuln');
        this.hideOrShowAttrs();
    },

    '.js-enable-vuln change': function () {
        this.hideOrShowAttrs();
    },

    hideOrShowAttrs: function () {
        if (this.enableCheckbox.is(':checked')) {
            this.hide();
        } else {
            this.show();
        }
    },

    show: function () {
        this.attrsElement.show();
    },

    hide: function () {
        this.attrsElement.hide();
    }
});

can.Control('CollapsibleField', {
    pluginName: 'collapsibleField',
    defaults: {
        placeholder: null,
        emptyValue: 'Click to set name'
    }
}, {
    init: function () {
        if (!this.options.placeholder || !this.options.placeholder.length) {
            this.placeholder = $('<span class="js-collapsible-field-placeholder collapsible-field-placeholder"></span>');
            this.placeholder.insertAfter(this.element);

        } else {
            this.placeholder = this.options.placeholder;
        }

        this.options.placeholder = this.placeholder;
        this.on();

        this.hide();
        this.refreshPlaceholderText();
    },

    '{placeholder} click': function (el, ev) {
        ev.preventDefault();
        this.show();
    },

    setPlaceholderText: function (text) {
        this.placeholder.text('(' + text + ')');
    },

    refreshPlaceholderText: function () {
        var text = this.element.val() || this.options.emptyValue;
        this.setPlaceholderText(text);
    },

    hide: function () {
        this.element.hide();
        this.placeholder.show();
    },

    show: function () {
        this.element.show();
        this.placeholder.hide();
        this.element.focus();
    }
});


can.Control('PHPSessionIdOverflowVulnerability', {
    pluginName: 'phpSessionIdOverflowVulnerability'
}, {
    init: function () {
        this.attrsElement = this.element.find('.js-vuln-attrs');
        this.enableCheckbox = this.element.find('.js-enable-vuln');
        this.hideOrShowAttrs();
    },

    '.js-enable-vuln change': function () {
        this.hideOrShowAttrs();
    },

    hideOrShowAttrs: function () {
        if (this.enableCheckbox.is(':checked')) {
            this.hide();
        } else {
            this.show();
        }
    },

    show: function () {
        this.attrsElement.show();
    },

    hide: function () {
        this.attrsElement.hide();
    }
});

jQuery(function ($) {
    var $controlForm = $('#controlForm');

    $('#contextName, #editModeCheckbox').on('change', function (ev) {
        $controlForm.submit();
    });

    $('.context-panel').contextControl();
});