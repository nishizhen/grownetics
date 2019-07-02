<?php
use Migrations\AbstractSeed;

/**
 * Wikis seed.
 */
class WikisSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {

        $rows = $this->fetchAll('SELECT * FROM wikis');
        $data = [
        [
                'id' => '1',
                'created' => '2017-01-26 22:22:03',
                'modified' => '2017-01-26 22:22:03',
                'label' => 'Home',
                'body' => 'Welcome to the Grow Facility Wiki! All of our how to\'s are in here so make sure to read through everything carefully. **Links in bold work.(Mission, partners, day-to-day)**

#**[Our Mission](/wikis/view/mission)**

- [How our facility differs](/wikis/view/ourfacility)
- [2017 goals](/wikis/view/goals)
- [**Partners**](/wikis/view/partners)

------
#**[Facility Operations](/wikis/view/facility-operations)**

- [**Day-to-Day**](/wikis/view/day-to-day)
- [Week-to-Week](/wikis/view/weektoweek)
- [Harvest batch cycles](/wikis/view/batchcycles)
- [Feeding schedule / Nutrient ratios](/wikis/view/feeding)

------
#**[Plant Care](/wikis/view/plant-care)**

- [How to keep clean](/wikis/view/cleanliness)
- [Common tools](/wikis/view/tools)

------
#**[Employee Directory](/wikis/view/employee-directory)**

- [Founders](/wikis/view/founders)
- [Growers, Admins, and Managers](/wikis/view/managers)
- [Crew](/wikis/view/crew)

------
#**[Our Dispensaries](/wikis/view/dispensaries)**

------
#**[Legal Policies](/wikis/view/legal)**

- [MetRC tag information](/wikis/view/metrc)
- [Medical compliance](/wikis/view/medical-legal)
- [Recreational compliance](/wikis/view/recreational-legal)
',
                'slug' => 'Home',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'add mission link',
            ],
            [
                'id' => '2',
                'created' => '2017-01-26 22:10:13',
                'modified' => '2017-01-26 22:10:13',
                'label' => 'Day-to-Day',
                'body' => '[< Home](/wikis/view/home)

Time | Task
------------ | -------------
8:00 AM | Prep your clothes and tool belt to begin the work day. 
9:00 AM | Check the dashboard overview and recent notifications. Try to find out where the last member of your crew left off.
10:00 AM | Walk room to room, inspecting each plant carefully. Remove any debris or dirt near the plants.
12:00 PM | Take a break
1:00 PM | Talk with your supervisor about what they need done immediately.
3:00 PM | Begin trimming session.
5:00 PM | Fill in sign out sheet, return all tools, and sign in to mark off tasks completed today.

- See also: [Week-to-Week](/wikis/view/weektoweek), [Harvest batch cycles](/wikis/view/batchcycles), [Feeding schedule / Nutrient ratios](/wikis/view/feeding)',
                'slug' => 'Day-to-Day',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'add indebting',
            ],
            [
                'id' => '3',
                'created' => '2017-01-26 22:10:38',
                'modified' => '2017-01-26 22:10:38',
                'label' => 'Mission',
                'body' => '[< Home](/wikis/view/home)

The Grow Facility\'s mission is to help people live better by safely bringing marijuana products to adult consumers (both medical and recreational) and deliver the most positive marijuana experience possible. We will provide quality and innovative products at the best value while maintaining strict adherence to regulatory requirements. Our trained staff guide consumers to the products that best meet their needs.

We will be the largest, most compliant and most profitable marijuana and accessories company in the state and nation by delivering the best possible marijuana experience to all of its consumers.

- See also: [2017 goals](/wikis/view/goals), [How our facility differs](/wikis/view/facility), [Partners](/wikis/view/partners)',
                'slug' => 'Mission',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'add home link',
            ],
            [
                'id' => '4',
                'created' => '2017-01-26 22:11:15',
                'modified' => '2017-01-26 22:11:15',
                'label' => 'Partners',
                'body' => '[< Home](/wikis/view/home)
### **Arduino**

- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Arduino_Logo.svg/640px-Arduino_Logo.svg.png" alt="alt text" width="150" height="110">
- We use custom sensors hand built by experienced technicians that cover over 9 data types. These sensors are built at the Boomtown accelerator in Boulder, Colorado.

------

### **Sparkfun**
- <img src="https://www.sparkfun.com/marcomm/SF-Logo-2C-PC-%C2%AE.jpg" width="150" height="110">
- Sparkfun sends us custom parts to put on the Arduino boards. 


- See also: [2017 goals](/wikis/view/goals), [How our facility differs](/wikis/view/facility)',
                'slug' => 'Partners',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'add bolding',
            ],
            [
                'id' => '5',
                'created' => '2017-01-26 22:15:33',
                'modified' => '2017-01-26 22:15:33',
                'label' => 'Daily-Operations',
                'body' => '',
                'slug' => 'Daily-Operations',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
            [
                'id' => '6',
                'created' => '2017-01-26 22:17:35',
                'modified' => '2017-01-26 22:17:35',
                'label' => 'Plant Care',
                'body' => '',
                'slug' => 'Plant-Care',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
            [
                'id' => '7',
                'created' => '2017-01-26 22:19:03',
                'modified' => '2017-01-26 22:19:03',
                'label' => 'Facility Operations',
                'body' => '',
                'slug' => 'Facility-Operations',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'add mission link',
            ],
            [
                'id' => '8',
                'created' => '2017-01-26 22:20:04',
                'modified' => '2017-01-26 22:20:04',
                'label' => 'Employee Directory',
                'body' => '',
                'slug' => 'Employee-Directory',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
            [
                'id' => '9',
                'created' => '2017-01-26 22:20:51',
                'modified' => '2017-01-26 22:20:51',
                'label' => 'Dispensaries',
                'body' => '',
                'slug' => 'Dispensaries',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
            [
                'id' => '10',
                'created' => '2017-01-26 22:21:13',
                'modified' => '2017-01-26 22:21:13',
                'label' => 'Legal',
                'body' => '',
                'slug' => 'Legal',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
            [
                'id' => '11',
                'created' => '2017-01-26 22:24:03',
                'modified' => '2017-01-26 22:24:03',
                'label' => 'Goals',
                'body' => '',
                'slug' => 'Goals',
                'version' => '1',
                'user_id' => '2',
                'change_message' => 'Page creation.',
            ],
        ];

        $table = $this->table('wikis');
        if ($rows == null) {
            $table->insert($data)->save();
        }
    }
}
