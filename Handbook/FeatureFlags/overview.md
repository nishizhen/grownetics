# Feature Flags

All new features should be released behind Feature Flags.

We have a plugin called FeatureFlags that does all the heavy lifting for
you. All you have to do is call it to find out if the new fancy code
should be executed or not.

## Dashboard

You can access the Feature Flags Dashboard by clicking `Admin` -> `Feature Flags`
in the left hand nav menu of any client interface.

## Best Practices

Feature Flags should be affirimative in enabling a new feature. For
example the flag to turn on the sending of notifications is called
`notification_sending_enabled` and it must be postitive rather than
`notification_sending_disabled` and it must be negative.

This is because all unset flags default to false. This allows us to roll
out new code with no functional or visible changes until we enable that
feature. If something goes wrong with the feature we can disable it
and everything goes back to how it used to be.

## Helper

The most commonly used bit is the helper.

### Displaying feature flag status

```
echo $this->FeatureFlags->getStatusBadge("notification_sending_enabled")
```

### Getting flag value for use in a conditional

```
$notification_sending_enabled = $this->FeatureFlags->getFlagValue("notification_sending_enabled")
```

### Outputting a link to enable/disable the feature

```
echo $this->FeatureFlags->getToggleLink("notification_sending_enabled")
```

## Behavior

If you need to access Feature Flags within a model's tabel, 
just include the behavior in the Tabel's initialize function 
`$this->addBehavior('FeatureFlags.FeatureFlags');`

Now you can query flags directly `$notifications_enabled = $this->getFeatureFlagValue("notification_sending_enabled");`

## Component

If you need to access Feature Flags within a Controller, you can use the Component that is
included in all Controllers by default via `AppController::initialize`.

You can query flags from controllers like so `$home_screen_enabled = $this->FeatureFlags->getFlagValue("home_screen")`