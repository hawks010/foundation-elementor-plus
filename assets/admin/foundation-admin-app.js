(function () {
  var wpElement = window.wp && window.wp.element;
  if (!wpElement) {
    return;
  }

  var h = wpElement.createElement;
  var useMemo = wpElement.useMemo;
  var useState = wpElement.useState;

  var boot = window.foundationElementorPlusAdmin || {};
  var settings = boot.settings || {};
  var copy = boot.copy || {};
  var widgets = Array.isArray(boot.widgets) ? boot.widgets : [];

  function cx() {
    return Array.prototype.slice.call(arguments).filter(Boolean).join(' ');
  }

  function MetricCard(props) {
    return h(
      'div',
      { className: 'fep-metric-card' },
      h('div', { className: 'fep-metric-label' }, props.label),
      h('div', { className: 'fep-metric-value' }, props.value),
      h('div', { className: 'fep-metric-meta' }, props.meta)
    );
  }

  function TabButton(props) {
    return h(
      'button',
      {
        type: 'button',
        className: cx('fep-tab', props.active && 'is-active'),
        id: props.id,
        role: 'tab',
        onClick: props.onClick,
        'aria-selected': props.active ? 'true' : 'false',
        'aria-controls': props.controls
      },
      props.children
    );
  }

  function TextField(props) {
    return h(
      'label',
      { className: 'fep-field' },
      h('span', { className: 'fep-field-label' }, props.label),
      h('span', { className: 'fep-field-help' }, props.help || ''),
      h('input', {
        type: 'text',
        className: 'fep-input',
        name: props.name,
        value: props.value,
        placeholder: props.placeholder || '',
        onChange: function (event) {
          props.onChange(event.target.value);
        }
      })
    );
  }

  function ToggleField(props) {
    return h(
      'label',
      { className: 'fep-toggle-row' },
      h('span', { className: 'fep-toggle-copy' },
        h('span', { className: 'fep-toggle-title' }, props.label),
        h('span', { className: 'fep-toggle-help' }, props.help || '')
      ),
      h('span', { className: 'fep-toggle' },
        h('input', {
          type: 'checkbox',
          name: props.name,
          value: 'yes',
          checked: !!props.checked,
          onChange: function (event) {
            props.onChange(event.target.checked);
          }
        }),
        h('span', { className: 'fep-toggle-ui', 'aria-hidden': 'true' })
      )
    );
  }

  function WidgetCard(props) {
    var widget = props.widget;
    return h(
      'label',
      { className: cx('fep-widget-card', widget.enabled && 'is-enabled') },
      h('div', { className: 'fep-widget-card__top' },
        h('div', { className: 'fep-widget-card__copy' },
          h('div', { className: 'fep-widget-card__title' }, widget.title),
          h('div', { className: 'fep-widget-card__description' }, widget.description)
        ),
        h('span', { className: cx('fep-badge', widget.enabled ? 'is-success' : 'is-muted') }, widget.enabled ? copy.enabledLabel : copy.disabledLabel)
      ),
      h('div', { className: 'fep-widget-card__bottom' },
        h('code', { className: 'fep-code' }, widget.id),
        h('span', { className: 'fep-checkbox' },
          h('input', {
            type: 'checkbox',
            name: 'foundation_elementor_plus_settings[enabled_widgets][]',
            value: widget.id,
            checked: widget.enabled,
            onChange: function (event) {
              props.onToggle(widget.id, event.target.checked);
            }
          }),
          h('span', { className: 'fep-checkbox-ui', 'aria-hidden': 'true' })
        )
      )
    );
  }

  function HiddenEnabledWidgetInputs(props) {
    return h(
      'div',
      { hidden: true, 'aria-hidden': 'true' },
      props.widgets
        .filter(function (widget) {
          return widget.enabled;
        })
        .map(function (widget) {
          return h('input', {
            key: widget.id,
            type: 'hidden',
            name: 'foundation_elementor_plus_settings[enabled_widgets][]',
            value: widget.id
          });
        })
    );
  }

  function OverviewPanel(props) {
    return h(
      'div',
      { className: 'fep-grid fep-grid--two' },
      h('section', { className: 'fep-panel' },
        h('div', { className: 'fep-panel-head' },
          h('div', null,
            h('div', { className: 'fep-panel-title' }, 'Command centre'),
            h('p', { className: 'fep-panel-body' }, copy.changesNotice)
          ),
          h('div', { className: 'fep-chip-row' },
            h('span', { className: 'fep-chip' }, 'On-demand widget assets'),
            h('span', { className: 'fep-chip' }, 'Lean registration'),
            h('span', { className: 'fep-chip' }, 'Editor-safe settings flow')
          )
        ),
        h('div', { className: 'fep-overview-list' },
          h('div', { className: 'fep-overview-item' },
            h('span', null, 'Plugin status'),
            h('strong', null, boot.isActive ? 'Active' : 'Inactive')
          ),
          h('div', { className: 'fep-overview-item' },
            h('span', null, 'Elementor'),
            h('strong', null, boot.elementorReady ? 'Connected' : 'Missing')
          ),
          h('div', { className: 'fep-overview-item' },
            h('span', null, 'Frontend scripts'),
            h('strong', null, props.state.deferWidgetScripts ? 'Deferred' : 'Standard')
          ),
          h('div', { className: 'fep-overview-item' },
            h('span', null, 'Legacy upload fix'),
            h('strong', null, props.state.enableLegacyUploadNormalization ? 'Enabled' : 'Disabled')
          )
        )
      ),
      h('section', { className: 'fep-panel fep-panel--sidebar' },
        h('div', { className: 'fep-panel-title' }, 'Quick actions'),
        h('div', { className: 'fep-action-stack' },
          h('a', { className: 'fep-button fep-button--secondary', href: boot.libraryUrl }, copy.openLibrary),
          h('a', { className: 'fep-button fep-button--ghost', href: boot.clearCacheUrl }, copy.clearCache),
          h('a', { className: 'fep-button fep-button--ghost', href: boot.settingsPageUrl }, copy.refresh)
        ),
        h('div', { className: 'fep-side-note' },
          h('strong', null, 'Why this matters'),
          h('p', null, 'Disabled widgets stop registering in Elementor and stop asking the browser to care about them. Less clutter, less drag, fewer tiny gremlins.')
        )
      )
    );
  }

  function WidgetsPanel(props) {
    var filtered = props.filteredWidgets;
    return h(
      'section',
      { className: 'fep-panel' },
      h('div', { className: 'fep-panel-head fep-panel-head--stack' },
        h('div', null,
          h('div', { className: 'fep-panel-title' }, 'Widget suite'),
          h('p', { className: 'fep-panel-body' }, 'Trim the suite to the widgets your site actually uses. Disabled widgets disappear from the Elementor panel and do not register their assets.')
        ),
        h('div', { className: 'fep-toolbar' },
          h('label', { className: 'fep-search' },
            h('span', { className: 'screen-reader-text' }, copy.widgetSearch),
            h('input', {
              type: 'search',
              className: 'fep-input',
              placeholder: copy.widgetSearchHint,
              value: props.search,
              onChange: function (event) {
                props.setSearch(event.target.value);
              }
            })
          ),
          h('div', { className: 'fep-button-row' },
            h('button', { type: 'button', className: 'fep-button fep-button--ghost', onClick: props.enableAll }, copy.enableAll),
            h('button', { type: 'button', className: 'fep-button fep-button--ghost', onClick: props.disableAll }, copy.disableAll)
          )
        )
      ),
      h('div', { className: 'fep-widget-grid' }, filtered.map(function (widget) {
        return h(WidgetCard, { key: widget.id, widget: widget, onToggle: props.onToggle });
      })),
      filtered.length === 0 ? h('p', { className: 'fep-empty-state' }, 'No widgets match that search.') : null
    );
  }

  function SettingsPanel(props) {
    return h(
      'section',
      { className: 'fep-panel' },
      h('div', { className: 'fep-panel-title' }, 'Editor settings'),
      h('p', { className: 'fep-panel-body' }, 'Keep the Elementor category tidy and friendly for editors while still using the native WordPress options save flow.'),
      h('div', { className: 'fep-grid fep-grid--two' },
        h(TextField, {
          label: 'Widget category label',
          help: 'Shown inside Elementor when browsing these widgets.',
          name: 'foundation_elementor_plus_settings[widget_category_label]',
          value: props.state.widgetCategoryLabel,
          placeholder: 'Foundation Plus',
          onChange: function (value) { props.setState('widgetCategoryLabel', value); }
        }),
        h(TextField, {
          label: 'Widget category icon',
          help: 'Elementor icon class for the Foundation category.',
          name: 'foundation_elementor_plus_settings[widget_category_icon]',
          value: props.state.widgetCategoryIcon,
          placeholder: 'fa fa-plug',
          onChange: function (value) { props.setState('widgetCategoryIcon', value); }
        })
      )
    );
  }

  function PerformancePanel(props) {
    return h(
      'section',
      { className: 'fep-panel' },
      h('div', { className: 'fep-panel-title' }, 'Performance & loading'),
      h('p', { className: 'fep-panel-body' }, 'These controls do not magically fix a chaotic page, but they stop the plugin from bringing extra luggage it does not need.'),
      h('div', { className: 'fep-toggle-stack' },
        h(ToggleField, {
          label: 'Defer widget scripts',
          help: 'Keeps frontend widget JavaScript out of the critical path where possible.',
          name: 'foundation_elementor_plus_settings[defer_widget_scripts]',
          checked: props.state.deferWidgetScripts,
          onChange: function (value) { props.setState('deferWidgetScripts', value); }
        }),
        h(ToggleField, {
          label: 'Legacy upload URL fix',
          help: 'Rewrites old beta upload URLs in Foundation content until the builder data is fully cleaned up.',
          name: 'foundation_elementor_plus_settings[enable_legacy_upload_normalization]',
          checked: props.state.enableLegacyUploadNormalization,
          onChange: function (value) { props.setState('enableLegacyUploadNormalization', value); }
        })
      )
    );
  }

  function IntegrationsPanel(props) {
    return h(
      'section',
      { className: 'fep-panel' },
      h('div', { className: 'fep-panel-title' }, 'Feed integrations'),
      h('p', { className: 'fep-panel-body' }, 'Optional defaults used by feed widgets. Widget-level settings can still override these values.'),
      h('div', { className: 'fep-grid fep-grid--two' },
        h(TextField, {
          label: 'YouTube API key',
          help: 'Optional, but recommended for more reliable YouTube data fetching.',
          name: 'foundation_elementor_plus_settings[youtube_api_key]',
          value: props.state.youtubeApiKey,
          placeholder: 'AIza...',
          onChange: function (value) { props.setState('youtubeApiKey', value); }
        }),
        h(TextField, {
          label: 'Default YouTube channel',
          help: 'Used when a widget-level channel is not set.',
          name: 'foundation_elementor_plus_settings[youtube_channel_source_default]',
          value: props.state.youtubeChannelSourceDefault,
          placeholder: '@inkfire or UC...',
          onChange: function (value) { props.setState('youtubeChannelSourceDefault', value); }
        }),
        h(TextField, {
          label: 'Instagram access token',
          help: 'Used for Instagram feed requests from a connected Business or Creator account.',
          name: 'foundation_elementor_plus_settings[instagram_access_token]',
          value: props.state.instagramAccessToken,
          placeholder: 'IGQVJ...',
          onChange: function (value) { props.setState('instagramAccessToken', value); }
        }),
        h(TextField, {
          label: 'Instagram business account ID',
          help: 'Used by social feed widgets when Instagram mode is enabled.',
          name: 'foundation_elementor_plus_settings[instagram_business_account_id]',
          value: props.state.instagramBusinessAccountId,
          placeholder: '1784...',
          onChange: function (value) { props.setState('instagramBusinessAccountId', value); }
        })
      )
    );
  }

  function App() {
    var initialState = useMemo(function () {
      return {
        widgetCategoryLabel: settings.widget_category_label || 'Foundation Plus',
        widgetCategoryIcon: settings.widget_category_icon || 'fa fa-plug',
        deferWidgetScripts: settings.defer_widget_scripts === 'yes',
        enableLegacyUploadNormalization: settings.enable_legacy_upload_normalization === 'yes',
        youtubeApiKey: settings.youtube_api_key || '',
        youtubeChannelSourceDefault: settings.youtube_channel_source_default || '',
        instagramAccessToken: settings.instagram_access_token || '',
        instagramBusinessAccountId: settings.instagram_business_account_id || '',
        widgets: widgets.map(function (widget) {
          return {
            id: widget.id,
            title: widget.title,
            description: widget.description,
            enabled: !!widget.enabled
          };
        })
      };
    }, []);

    var _useState = useState(initialState),
      state = _useState[0],
      setStateValue = _useState[1];
    var _useState2 = useState('overview'),
      activeTab = _useState2[0],
      setActiveTab = _useState2[1];
    var _useState3 = useState(''),
      search = _useState3[0],
      setSearch = _useState3[1];

    var setState = function (key, value) {
      setStateValue(function (prev) {
        var next = Object.assign({}, prev);
        next[key] = value;
        return next;
      });
    };

    var setWidgetState = function (widgetId, enabled) {
      setStateValue(function (prev) {
        return Object.assign({}, prev, {
          widgets: prev.widgets.map(function (widget) {
            return widget.id === widgetId ? Object.assign({}, widget, { enabled: enabled }) : widget;
          })
        });
      });
    };

    var enableAll = function () {
      setStateValue(function (prev) {
        return Object.assign({}, prev, {
          widgets: prev.widgets.map(function (widget) {
            return Object.assign({}, widget, { enabled: true });
          })
        });
      });
    };

    var disableAll = function () {
      setStateValue(function (prev) {
        return Object.assign({}, prev, {
          widgets: prev.widgets.map(function (widget) {
            return Object.assign({}, widget, { enabled: false });
          })
        });
      });
    };

    var filteredWidgets = useMemo(function () {
      if (!search) {
        return state.widgets;
      }

      var needle = search.toLowerCase();
      return state.widgets.filter(function (widget) {
        return widget.title.toLowerCase().indexOf(needle) > -1 || widget.description.toLowerCase().indexOf(needle) > -1 || widget.id.toLowerCase().indexOf(needle) > -1;
      });
    }, [search, state.widgets]);

    var enabledCount = useMemo(function () {
      return state.widgets.filter(function (widget) { return widget.enabled; }).length;
    }, [state.widgets]);

    var serializedState = JSON.stringify({
      widgetCategoryLabel: state.widgetCategoryLabel,
      widgetCategoryIcon: state.widgetCategoryIcon,
      deferWidgetScripts: state.deferWidgetScripts,
      enableLegacyUploadNormalization: state.enableLegacyUploadNormalization,
      youtubeApiKey: state.youtubeApiKey,
      youtubeChannelSourceDefault: state.youtubeChannelSourceDefault,
      instagramAccessToken: state.instagramAccessToken,
      instagramBusinessAccountId: state.instagramBusinessAccountId,
      widgets: state.widgets.map(function (widget) { return { id: widget.id, enabled: widget.enabled }; })
    });

    var isDirty = serializedState !== JSON.stringify({
      widgetCategoryLabel: initialState.widgetCategoryLabel,
      widgetCategoryIcon: initialState.widgetCategoryIcon,
      deferWidgetScripts: initialState.deferWidgetScripts,
      enableLegacyUploadNormalization: initialState.enableLegacyUploadNormalization,
      youtubeApiKey: initialState.youtubeApiKey,
      youtubeChannelSourceDefault: initialState.youtubeChannelSourceDefault,
      instagramAccessToken: initialState.instagramAccessToken,
      instagramBusinessAccountId: initialState.instagramBusinessAccountId,
      widgets: initialState.widgets.map(function (widget) { return { id: widget.id, enabled: widget.enabled }; })
    });

    return h(
      'div',
      { className: 'fep-admin-app' },
      h(HiddenEnabledWidgetInputs, { widgets: state.widgets }),
      boot.cacheCleared ? h('div', { className: 'notice notice-success is-dismissible fep-runtime-notice' }, h('p', null, copy.cacheClearedNotice)) : null,
      h('section', { className: 'fep-hero' },
        h('div', { className: 'fep-hero__copy' },
          h('div', { className: 'fep-eyebrow' }, 'Foundation Suite • v' + (boot.version || '')), 
          h('h1', { className: 'fep-hero__title' }, copy.heroTitle),
          h('p', { className: 'fep-hero__body' }, copy.heroBody),
          h('div', { className: 'fep-button-row' },
            h('button', { type: 'submit', className: 'fep-button fep-button--primary' }, copy.saveLabel),
            h('a', { className: 'fep-button fep-button--secondary', href: boot.clearCacheUrl }, copy.clearCache),
            h('a', { className: 'fep-button fep-button--ghost', href: boot.libraryUrl }, copy.openLibrary)
          )
        ),
        h('div', { className: 'fep-hero__stats' },
          h(MetricCard, { label: 'Plugin status', value: boot.isActive ? 'Active' : 'Inactive', meta: 'Elementor suite loaded' }),
          h(MetricCard, { label: 'Elementor', value: boot.elementorReady ? 'Connected' : 'Missing', meta: 'Widget registration pipeline' }),
          h(MetricCard, { label: 'Enabled widgets', value: String(enabledCount), meta: String(boot.widgetCount || state.widgets.length) + ' available in the suite' }),
          h(MetricCard, { label: 'Frontend loading', value: state.deferWidgetScripts ? 'Deferred' : 'Standard', meta: 'Assets only enqueue when needed' })
        )
      ),
      h('nav', { className: 'fep-tabs', 'aria-label': 'Foundation admin sections', role: 'tablist' },
        h(TabButton, { id: 'fep-tab-overview', controls: 'fep-panel-overview', active: activeTab === 'overview', onClick: function () { setActiveTab('overview'); } }, 'Overview'),
        h(TabButton, { id: 'fep-tab-widgets', controls: 'fep-panel-widgets', active: activeTab === 'widgets', onClick: function () { setActiveTab('widgets'); } }, 'Widgets'),
        h(TabButton, { id: 'fep-tab-settings', controls: 'fep-panel-settings', active: activeTab === 'settings', onClick: function () { setActiveTab('settings'); } }, 'Editor'),
        h(TabButton, { id: 'fep-tab-performance', controls: 'fep-panel-performance', active: activeTab === 'performance', onClick: function () { setActiveTab('performance'); } }, 'Performance'),
        h(TabButton, { id: 'fep-tab-integrations', controls: 'fep-panel-integrations', active: activeTab === 'integrations', onClick: function () { setActiveTab('integrations'); } }, 'Integrations')
      ),
      isDirty ? h('div', { className: 'fep-dirty-banner' }, 'You have unsaved changes.') : null,
      h('div', { id: 'fep-panel-overview', role: 'tabpanel', 'aria-labelledby': 'fep-tab-overview', hidden: activeTab !== 'overview', 'aria-hidden': activeTab !== 'overview' }, h(OverviewPanel, { state: state })),
      h('div', { id: 'fep-panel-widgets', role: 'tabpanel', 'aria-labelledby': 'fep-tab-widgets', hidden: activeTab !== 'widgets', 'aria-hidden': activeTab !== 'widgets' }, h(WidgetsPanel, {
        filteredWidgets: filteredWidgets,
        search: search,
        setSearch: setSearch,
        enableAll: enableAll,
        disableAll: disableAll,
        onToggle: setWidgetState
      })),
      h('div', { id: 'fep-panel-settings', role: 'tabpanel', 'aria-labelledby': 'fep-tab-settings', hidden: activeTab !== 'settings', 'aria-hidden': activeTab !== 'settings' }, h(SettingsPanel, { state: state, setState: setState })),
      h('div', { id: 'fep-panel-performance', role: 'tabpanel', 'aria-labelledby': 'fep-tab-performance', hidden: activeTab !== 'performance', 'aria-hidden': activeTab !== 'performance' }, h(PerformancePanel, { state: state, setState: setState })),
      h('div', { id: 'fep-panel-integrations', role: 'tabpanel', 'aria-labelledby': 'fep-tab-integrations', hidden: activeTab !== 'integrations', 'aria-hidden': activeTab !== 'integrations' }, h(IntegrationsPanel, { state: state, setState: setState }))
    );
  }

  var target = document.getElementById('foundation-elementor-plus-admin-root');
  if (!target) {
    return;
  }

  if (typeof wpElement.createRoot === 'function') {
    wpElement.createRoot(target).render(h(App));
  } else if (typeof wpElement.render === 'function') {
    wpElement.render(h(App), target);
  }
})();
