# PagerDuty

On call scheduling and incident handling is all through PagerDuty. PagerDuty gets alerts from several sources including Pingdom, Zendesk, and Grafana.

## You've received a page from Pingdom, what next?

A ticket from Pingdom means a service has gone down and it's your job to investigate. Mark the issue as Acknowledged in PagerDuty and start going through the debug checklist for that service. A good place to start looking for information is [Dev Ops](dev-ops)

If an onsite facility is offline, call the contacts for the client on the Master Client List. Inform them we are seeing an outage on their end. Ask if their power is on. Ask how we can help.

## You've received a page from Zendesk, what next?

A ticket from Zendesk is a customer support ticket and should be handled through the ZenDesk interface as soon as possible. After initial customer response is given, you can mark the incident as Resolved in PagerDuty, and use ZenDesk to track it's progress to completion.

You may need to reference the [Master Clients List](https://docs.google.com/spreadsheets/d/1s8Ox_82EWDONmTTmy-WRBpw4Av-fKEwSLYSFHLm-VJo/edit) page to get more info about them so you can service them faster.

1. Apologize that they are having troubles and assure them you're going to look into the issue immediately, but make no statements about specific timeframes. If they ask for one, say you're not sure, you need to investigate the problem more first.

## Helping Debug

If a user is having a rendering issue that you don't see, you can impersonate them to try and debug further.

Go to Settings->Users, then click 'Impersonate' next to the user having problems. You can now see everything they would see, so you can go to the page causing them trouble to see if you see the same problem.

Make sure you're matching their browser as closely as possible, and if you are going to make a new issue for this bug, this is a good time to take screenshots.

## Hardware Issues

1. Have they power cycled the affected device? (Turn it off and back on again.)
2. Have them unplug and re-seat all wires on the affected device.
3. Are any wires damaged, frayed?

## Software Issues

1. What is the issue, is it a known issue (already in GitLab?)
2. If it's a memory or HDD issue, try and clear some space by killing processes or removing old logfiles / large files. Try not to remove recent logfiles as they may not have been backed up to S3 yet.