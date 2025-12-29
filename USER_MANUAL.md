# ARI Dialer - User Manual

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Managing Campaigns](#managing-campaigns)
4. [IVR Menus](#ivr-menus)
5. [Call Detail Records (CDR)](#call-detail-records-cdr)
6. [Real-Time Monitoring](#real-time-monitoring)
7. [Settings](#settings)
8. [Language Selection](#language-selection)
9. [Best Practices](#best-practices)
10. [Common Scenarios](#common-scenarios)

---

## Getting Started

### Accessing the System

1. **Open Your Web Browser**
   - Chrome, Firefox, Safari, or Edge (latest versions recommended)

2. **Navigate to the ARI Dialer URL**
   ```
   http://YOUR_SERVER_IP/adial
   ```
   or
   ```
   http://localhost/adial
   ```

3. **Login (if authentication is enabled)**
   - Enter your username and password
   - Click "Login"

### First Time Login

When you first access ARI Dialer, you'll see the Dashboard with:
- Statistics overview (Total Campaigns, Active Calls, Today's Calls)
- Quick access buttons to main sections
- Language switcher in the top-right corner (EN/RU)

### Main Navigation Menu

The left sidebar contains:
- **Dashboard** - Overview and statistics
- **Campaigns** - Manage dialing campaigns
- **IVR Menus** - Interactive Voice Response setup
- **CDR** - Call Detail Records and reports
- **Monitoring** - Real-time call monitoring
- **Settings** - System configuration

---

## Dashboard Overview

The Dashboard provides a quick overview of your dialing system.

### Key Metrics

**Today's Statistics:**
- **Total Calls** - Number of calls made today
- **Answered Calls** - Successfully answered calls
- **Answer Rate** - Percentage of answered calls
- **Average Talk Time** - Mean duration of conversations

**Campaign Overview:**
- **Total Campaigns** - All campaigns in the system
- **Active Campaigns** - Currently running campaigns
- **Pending Numbers** - Numbers waiting to be dialed

**System Status:**
- **Active Channels** - Current ongoing calls
- **Asterisk Status** - PBX system health
- **Database Status** - Database connectivity

### Quick Actions

From the Dashboard, you can:
- Click "New Campaign" to create a campaign
- View campaign details by clicking campaign names
- Access real-time monitoring
- Navigate to any section using the menu

---

## Managing Campaigns

Campaigns are the core feature of ARI Dialer. A campaign dials a list of phone numbers and connects answered calls to agents.

### Creating a New Campaign

#### Step 1: Access Campaign Creation

1. Click **"Campaigns"** in the main menu
2. Click the **"+ New Campaign"** button (top-right)

#### Step 2: Fill Campaign Details

**Basic Information:**

| Field | Description | Example |
|-------|-------------|---------|
| **Campaign Name** | Unique identifier for this campaign | "Sales Outreach Q4" |
| **Description** | Brief description of campaign purpose | "Follow-up with Q3 leads" |
| **Status** | Initial campaign state | "Stopped" (default) |

**Trunk Configuration:**

| Field | Description | Example |
|-------|-------------|---------|
| **Trunk Type** | Method to dial out | PJSIP, SIP, or Custom |
| **Trunk Value** | Trunk name or custom string | "PJSIP/my-trunk" or "PJSIP/100" |

**Trunk Type Options:**
- **PJSIP** - Modern SIP protocol (recommended)
  - Example: `PJSIP/trunk-name`
- **SIP** - Legacy SIP protocol
  - Example: `SIP/trunk-name`
- **Custom** - Custom dial string
  - Example: `Local/100@from-internal`

**Agent Destination:**

Where answered calls should be connected:

| Option | Description | Example |
|--------|-------------|---------|
| **Call Extension** | Direct to specific extension | "PJSIP/100" |
| **IVR Menu** | Send to IVR for caller input | Select from dropdown |
| **Custom** | Custom dialplan destination | "Queue(sales-queue)" |

**Call Settings:**

| Field | Description | Recommended |
|-------|-------------|-------------|
| **Concurrent Calls** | Maximum simultaneous calls | Start with 1-5 |
| **Retry Times** | Number of retry attempts | 2-3 times |
| **Retry Delay** | Seconds between retries | 300 (5 minutes) |
| **Record Calls** | Enable call recording | ✓ (Yes) |
| **Caller ID** | Outbound caller ID | "+1234567890" |

#### Step 3: Add Phone Numbers

**Option A: Upload CSV File**

1. Prepare a CSV file with phone numbers:
   ```csv
   phone_number,data
   +1234567890,{"name":"John Doe","reference":"REF001"}
   +0987654321,{"name":"Jane Smith","reference":"REF002"}
   ```

2. Click **"Upload CSV"** button
3. Select your file
4. Click **"Import"**

**CSV Format Requirements:**
- Column 1: `phone_number` (required)
- Column 2: `data` (optional) - JSON format for additional data
- No header row required if only phone numbers

**Option B: Manual Entry**

1. Click **"Add Number Manually"**
2. Enter phone number: `+1234567890`
3. Optionally add JSON data: `{"name":"Customer Name"}`
4. Click **"Add"**

#### Step 4: Save and Configure

1. Review all settings
2. Click **"Create Campaign"** button
3. You'll be redirected to the campaign list

### Starting a Campaign

1. Go to **Campaigns** → **View All Campaigns**
2. Find your campaign in the list
3. Click the **green "Play" button** ▶️
4. Campaign status changes to **"Running"** (green badge)

**What Happens:**
- System starts dialing numbers from the list
- Respects concurrent call limit
- Connects answered calls to agent destination
- Automatically moves to next number

### Monitoring a Running Campaign

While campaign is running, you can see:

**Campaign Statistics:**
- **Total Numbers** - All numbers in campaign
- **Pending** - Not yet dialed
- **Calling** - Currently being dialed
- **Answered** - Successfully connected
- **Completed** - Finished calls
- **Failed** - Unsuccessful attempts

**Control Actions:**

#### ✅ **Verified Campaign Control Logic**

**🛑 STOP Campaign:**
- ✅ **Immediately stops** all new dial attempts
- ✅ **Hangs up all active calls** for this campaign
- ✅ **Resets ALL numbers to 'pending'** status
- ✅ **Resets attempt counters to 0**
- ⚠️ **Complete restart** - campaign starts fresh when restarted

**⏸️ PAUSE Campaign:**
- ✅ **Stops new dial attempts** immediately
- ✅ **Keeps all active calls running** (no hangups)
- ✅ **Preserves number statuses** (calling, answered, etc.)
- ✅ **Preserves attempt counters**
- 🔄 **True pause** - can resume exactly where it left off

**▶️ START/RESUME Campaign:**
- ✅ **Continues from current state** if previously paused
- ✅ **Starts fresh** if previously stopped (numbers reset)
- ✅ **Respects concurrent call limits**
- ✅ **Processes pending numbers only**

#### 💡 **Usage Tips:**
- Use **PAUSE** for temporary breaks (lunch, meetings)
- Use **STOP** to completely reset a campaign
- **RESUME** works seamlessly after PAUSE
- Monitor real-time on the **Monitoring** page

### Viewing Campaign Details

1. Click campaign **name** in the list
2. View detailed page showing:
   - Campaign configuration
   - Real-time statistics
   - Number list with statuses
   - Call history

**Number Status Indicators:**
- 🟡 **Pending** - Not yet called
- 🔵 **Calling** - Currently dialing
- 🟢 **Answered** - Call connected
- 🔴 **Failed** - Call failed
- ⚫ **Completed** - Call finished

### Editing a Campaign

1. Click **"Edit"** button (pencil icon) 📝
2. Modify campaign settings
3. Click **"Update Campaign"**

**Note:** Cannot edit trunk settings while campaign is running.

### Adding More Numbers to Existing Campaign

1. Open campaign details
2. Scroll to **"Campaign Numbers"** section
3. Click **"Add Numbers"**
4. Upload CSV or add manually
5. New numbers are added to pending queue

### Deleting a Campaign

1. **Stop** the campaign first (must be in "Stopped" status)
2. Click **"Delete"** button (trash icon) 🗑️
3. Confirm deletion

**Warning:** This action:
- Deletes all campaign numbers
- Removes associated IVR menus
- Preserves CDR records (call history)
- **Cannot be undone**

---

## IVR Menus

Interactive Voice Response (IVR) menus allow callers to interact with the system using their phone keypad.

### Understanding IVR Flow

```
Call Answered → Play Audio Prompt → Wait for DTMF Input → Execute Action
```

**Example Scenario:**
```
Audio: "Press 1 for Sales, 2 for Support, 3 for Billing"
Caller presses 2 → Transferred to Support Queue
```

### Creating an IVR Menu

#### Step 1: Start IVR Creation

1. Click **"IVR Menus"** in main menu
2. Click **"+ New IVR Menu"** button

#### Step 2: Basic Settings

| Field | Description | Example |
|-------|-------------|---------|
| **Campaign** | Associate with campaign | Select from dropdown |
| **Menu Name** | Descriptive name | "Main Menu" |
| **Audio File** | Greeting/prompt file | Upload WAV or MP3 |
| **Timeout** | Seconds to wait for input | 3-5 seconds |
| **Max Digits** | Maximum DTMF digits | 1 (for single digit) |

#### Step 3: Upload Audio File

**Audio Requirements:**
- **Format:** WAV or MP3
- **Sample Rate:** 8000Hz (recommended)
- **Channels:** Mono
- **Duration:** Keep under 30 seconds

**Uploading Audio:**
1. Click **"Choose File"**
2. Select your audio file
3. System automatically converts to Asterisk format

**Audio File Tips:**
- Use clear, professional voice
- Keep instructions concise
- Speak slowly and clearly
- Example: "Thank you for calling. Press 1 for Sales, Press 2 for Support, or stay on the line for an operator."

#### Step 4: Configure DTMF Actions

Add actions for each key press:

**Example Configuration:**

| DTMF Digit | Action Type | Action Value | Description |
|------------|-------------|--------------|-------------|
| **1** | Call Extension | PJSIP/101 | Transfer to Sales |
| **2** | Call Extension | PJSIP/102 | Transfer to Support |
| **3** | Queue | sales-queue | Put in queue |
| **9** | Playback | thank-you | Play message |
| **#** | Hangup | - | End call |
| **i** (invalid) | Playback | invalid-option | Invalid input |
| **t** (timeout) | Call Extension | PJSIP/100 | No input received |

**DTMF Digit Options:**
- **0-9** - Numeric keys
- **\*** - Star key
- **#** - Hash/Pound key
- **i** - Invalid input (not a valid option)
- **t** - Timeout (no input received)

**Action Types:**

1. **Call Extension**
   - Transfers to specific extension
   - Example: `PJSIP/100`, `SIP/200`, `Local/100@internal`

2. **Queue**
   - Places caller in a queue
   - Example: `sales-queue`, `support-queue`

3. **Hangup**
   - Ends the call
   - No value needed

4. **Playback**
   - Plays audio file
   - Example: `thank-you`, `goodbye`, `hours`
   - File must exist in Asterisk sounds directory

5. **Go to IVR**
   - Chains to another IVR menu
   - Select from available IVR menus

#### Step 5: Add Multiple Actions

1. Click **"+ Add Action"** button
2. Configure each DTMF digit
3. Ensure at least one action exists
4. Add 'i' and 't' handlers for better UX

**Best Practice:**
Always include:
- At least 2-3 valid options (1, 2, 3)
- Invalid input handler (i)
- Timeout handler (t)
- Optional: 0 for operator, # to repeat menu

#### Step 6: Save IVR Menu

1. Review all settings
2. Click **"Create IVR Menu"**
3. Menu is now available for campaigns

### Testing an IVR Menu

1. Go to **IVR Menus** → Select your menu
2. Click **"View"** to see details
3. Click **"Play Audio"** to preview greeting
4. Test with a real call to verify DTMF actions

### Editing IVR Menus

1. Navigate to **IVR Menus**
2. Click **"Edit"** (pencil icon)
3. Modify settings
4. Upload new audio if needed
5. Update DTMF actions
6. Click **"Update IVR Menu"**

**Note:** Changes take effect immediately for new calls.

### Using IVR in Campaigns

**Method 1: Set as Agent Destination**
1. Create/Edit campaign
2. Set **Agent Destination Type** to "IVR Menu"
3. Select your IVR menu from dropdown
4. Save campaign

**Method 2: Chain from Another IVR**
1. In IVR action configuration
2. Set action type to "Go to IVR"
3. Select target IVR menu

**Example Multi-Level IVR:**
```
Main Menu (IVR 1):
  Press 1 → Sales Menu (IVR 2)
  Press 2 → Support Menu (IVR 3)

Sales Menu (IVR 2):
  Press 1 → New Sales (PJSIP/201)
  Press 2 → Existing Customers (PJSIP/202)
```

---

## Call Detail Records (CDR)

CDR provides complete call history and reporting.

### Accessing CDR

1. Click **"CDR"** in the main menu
2. You'll see a table of all call records

### Understanding CDR Table

**Columns Explained:**

| Column | Description | Example |
|--------|-------------|---------|
| **Call Date/Time** | When call started | 2024-11-14 14:30:45 |
| **Campaign** | Associated campaign | "Sales Q4" |
| **Caller ID** | Outbound caller ID | +1234567890 |
| **Destination** | Number called | +0987654321 |
| **Agent** | Agent who received call | PJSIP/100 |
| **Duration** | Total call time | 00:05:30 |
| **Billsec** | Talk time (after answer) | 00:04:45 |
| **Disposition** | Call outcome | Answered |
| **Recording** | Audio file (if enabled) | 🎵 Play button |

**Disposition Types:**

| Status | Meaning | Icon |
|--------|---------|------|
| **Answered** | Call was answered | 🟢 Green |
| **No Answer** | No one answered | 🟡 Yellow |
| **Busy** | Line was busy | 🟠 Orange |
| **Failed** | Call failed | 🔴 Red |
| **Cancelled** | Call was cancelled | ⚫ Gray |

### Filtering CDR Records

Use the filter section to narrow down results:

**Filter Options:**

1. **Date Range**
   - From Date: Select start date
   - To Date: Select end date
   - Click "Filter"

2. **Campaign Filter**
   - Select specific campaign from dropdown
   - Shows only calls from that campaign

3. **Disposition Filter**
   - Filter by: All, Answered, No Answer, Busy, Failed
   - Useful for finding unsuccessful calls

4. **Search Box**
   - Search by phone number, caller ID, or agent
   - Real-time filtering

**Example Filters:**

```
Scenario 1: Find all answered calls today
- Date: Today → Today
- Disposition: Answered
- Click "Filter"

Scenario 2: Find failed calls for specific campaign
- Campaign: "Sales Q4"
- Disposition: Failed
- Date: Last 7 days
- Click "Filter"

Scenario 3: Search specific number
- Search box: "+1234567890"
- Results show all calls to/from this number
```

### Listening to Call Recordings

If recording was enabled:

1. Find the call record in CDR
2. Click the **"Play"** button (🎵 icon) in Recording column
3. Audio player modal opens
4. Use controls to:
   - ▶️ Play
   - ⏸️ Pause
   - 🔊 Adjust volume
   - ⬇️ Download recording

**Recording File Names:**
- Format: `campaign_id-number-timestamp.wav` or `.mp3`
- Example: `5-1234567890-20241114143045.wav`

### Exporting CDR Data

1. Apply filters (optional)
2. Click **"Export to CSV"** button
3. Browser downloads CSV file
4. Open in Excel, Google Sheets, or other tools

**CSV Contains:**
- All visible columns
- Filtered data only
- Timestamp in ISO format
- Duration in seconds

**Example Uses:**
- Import to CRM
- Create custom reports
- Billing calculations
- Performance analysis

### CDR Statistics

View quick statistics at the top:

- **Total Calls:** All calls in filtered range
- **Answered:** Successfully connected calls
- **Answer Rate:** Percentage answered
- **Average Duration:** Mean call length

---

## Real-Time Monitoring

Monitor live system activity and ongoing campaigns.

### Accessing Monitoring

1. Click **"Monitoring"** in main menu
2. Dashboard auto-refreshes every 3 seconds

### Today's Statistics Panel

**Key Metrics (Updates Real-Time):**

```
┌─────────────────────────────────────────────┐
│  Total Calls Today: 245                     │
│  Answered Calls: 178                        │
│  Answer Rate: 72.65%                        │
│  Avg Talk Time: 00:03:45                    │
└─────────────────────────────────────────────┘
```

### Active Campaigns Section

Shows all currently running campaigns:

**Information Displayed:**
- Campaign name (clickable)
- Status badge (Running/Paused)
- Concurrent calls setting
- Current progress

**Example:**
```
┌─────────────────────────────────────┐
│ Sales Q4                            │
│ Status: Running   Concurrent: 5     │
│ Progress: 45/100                    │
└─────────────────────────────────────┘
```

### Active Calls/Channels

Real-time view of ongoing calls:

| Channel ID | State | Caller | Connected |
|------------|-------|--------|-----------|
| PJSIP/100-0001 | Up | +1234567890 | PJSIP/200 |
| PJSIP/101-0002 | Ringing | +0987654321 | - |

**Channel States:**
- **Up** - Call connected and active
- **Ringing** - Call is ringing
- **Dialing** - Outbound call in progress
- **Down** - Call ended

### Campaign Statistics Table

Detailed breakdown for each active campaign:

| Campaign | Status | Total | Pending | Calling | Answered | Completed | Failed | Progress |
|----------|--------|-------|---------|---------|----------|-----------|--------|----------|
| Sales Q4 | Running | 100 | 45 | 5 | 30 | 35 | 20 | 55% |

**Progress Bar:**
- Visual representation of completion
- Green = completed
- Updates in real-time

### Auto-Update Indicator

Top-right corner shows:
- 🟢 **Auto Updating** - System is refreshing data
- 🔴 **Update Failed** - Connection issue

**Manual Refresh:**
- Click browser refresh if auto-update stops
- Check network connection if errors persist

### What to Monitor

**Normal Operation:**
- Active channels ≤ Total concurrent calls
- Pending count decreasing
- Answered calls increasing
- No excessive failed calls

**Warning Signs:**
- 🚨 All calls failing (check trunk)
- 🚨 No active channels but campaign running (check Asterisk)
- 🚨 High failed rate >50% (check numbers/trunk)
- 🚨 Answered but no talk time (check agent destination)

---

## Settings

Configure system-wide options.

### Accessing Settings

1. Click **"Settings"** in main menu
2. View/edit configuration options

### Available Settings

| Setting | Description | Default |
|---------|-------------|---------|
| **ARI Enabled** | Enable/Disable ARI | Enabled |
| **Debug Mode** | Verbose logging | Enabled |
| **Max Concurrent Campaigns** | Maximum running campaigns | 5 |
| **Call Timeout** | Seconds before timeout | 60 |

### Modifying Settings

1. Find setting to change
2. Click **"Edit"** button
3. Update value
4. Click **"Save"**
5. Changes apply immediately

**Note:** Some settings may require service restart.

---

## Language Selection

ARI Dialer supports multiple languages.

### Changing Language

1. Look at **top-right corner** of any page
2. You'll see language buttons: **EN** | **RU**
3. Click desired language:
   - **EN** - English
   - **RU** - Russian (Русский)

**What Changes:**
- All menu items
- Button labels
- Table headers
- Form labels
- Messages and notifications
- Help text

**Language Persistence:**
- Selection saved in browser cookie (30 days)
- Remains active across sessions
- Per-user preference

### Supported Languages

- 🇬🇧 **English** - Full translation
- 🇷🇺 **Russian** - Полный перевод

---

## Best Practices

### Campaign Management

✅ **DO:**
- Start with low concurrent calls (1-5) for testing
- Use descriptive campaign names
- Test with small number list first
- Monitor first few calls of each campaign
- Enable call recording for quality assurance
- Set appropriate retry delays (300-600 seconds)
- Review failed calls and adjust

❌ **DON'T:**
- Set concurrent calls too high initially
- Use generic names like "Campaign 1"
- Upload invalid phone numbers
- Start large campaigns without testing
- Ignore high failure rates
- Set retry delay too short (<60 seconds)

### IVR Design

✅ **DO:**
- Keep menu options to 3-4 maximum
- Use clear, professional audio
- Add timeout and invalid handlers
- Test IVR before using in production
- Provide option to reach operator (0)
- Keep audio prompts under 20 seconds

❌ **DON'T:**
- Create too many nested menus (>2 levels)
- Use poor quality audio
- Forget timeout handlers
- Make prompts too long
- Omit option to speak to agent

### Audio Files

✅ **DO:**
- Convert to 8000Hz mono WAV
- Use professional voice talent
- Test playback before uploading
- Keep file sizes reasonable
- Use descriptive file names

❌ **DON'T:**
- Use high bitrate stereo files
- Record with background noise
- Use compression artifacts
- Upload untested files

### Phone Number Lists

✅ **DO:**
- Use E.164 format: +[country][number]
- Remove duplicates before upload
- Validate numbers before importing
- Include country codes
- Clean up old campaigns

❌ **DON'T:**
- Mix number formats
- Include invalid numbers
- Upload same list multiple times
- Forget to include area codes

---

## Common Scenarios

### Scenario 1: Simple Sales Outreach Campaign

**Goal:** Call 100 leads and connect answered calls to sales agent.

**Steps:**

1. **Create Campaign**
   - Name: "November Sales Leads"
   - Trunk: PJSIP/sales-trunk
   - Agent Destination: Call Extension → PJSIP/100
   - Concurrent Calls: 3
   - Record Calls: Yes

2. **Prepare CSV**
   ```csv
   +12125551001
   +12125551002
   +12125551003
   ```

3. **Upload Numbers**
   - Upload CSV file
   - Verify 100 numbers imported

4. **Start Campaign**
   - Click Play button
   - Monitor in real-time

5. **Monitor Progress**
   - Check monitoring dashboard
   - Review answered calls
   - Adjust concurrent calls if needed

6. **Review Results**
   - Go to CDR
   - Filter by campaign
   - Export results
   - Listen to recordings

---

### Scenario 2: Customer Survey with IVR

**Goal:** Call customers, play survey, collect responses via DTMF.

**Steps:**

1. **Create IVR Menu**
   - Name: "Satisfaction Survey"
   - Audio: "Rate your experience: 1 for poor, 2 for good, 3 for excellent"
   - Timeout: 5 seconds

2. **Configure DTMF Actions**
   - 1 → Playback: "thank-you"
   - 2 → Playback: "thank-you"
   - 3 → Playback: "thank-you"
   - t → Playback: "no-response"

3. **Create Campaign**
   - Name: "Customer Survey Nov 2024"
   - Agent Destination: IVR Menu → "Satisfaction Survey"
   - Concurrent Calls: 5

4. **Upload Customer List**
   ```csv
   phone_number,data
   +12125551001,{"customer_id":"C001","name":"John Doe"}
   +12125551002,{"customer_id":"C002","name":"Jane Smith"}
   ```

5. **Start and Monitor**
   - Start campaign
   - Monitor completion rate
   - Review CDR for responses

6. **Analyze Results**
   - Export CDR
   - Analyze DTMF inputs
   - Generate report

---

### Scenario 3: Multi-Department IVR

**Goal:** Route calls to different departments based on caller input.

**Steps:**

1. **Create Main Menu IVR**
   - Name: "Main Menu"
   - Audio: "Press 1 for Sales, 2 for Support, 3 for Billing"
   - Actions:
     - 1 → Call Extension: PJSIP/201 (Sales)
     - 2 → Call Extension: PJSIP/202 (Support)
     - 3 → Call Extension: PJSIP/203 (Billing)
     - 0 → Call Extension: PJSIP/100 (Operator)
     - i → Playback: "invalid-option"
     - t → Call Extension: PJSIP/100 (Operator)

2. **Create Campaign**
   - Name: "Customer Callback Service"
   - Agent Destination: IVR → "Main Menu"
   - Concurrent Calls: 10

3. **Upload Callback List**
   - Import customers requesting callbacks

4. **Start Campaign**
   - Customers receive calls
   - Choose department via keypad
   - Connected to appropriate agent

---

### Scenario 4: Appointment Reminders

**Goal:** Call patients to remind them of appointments.

**Steps:**

1. **Create IVR**
   - Name: "Appointment Reminder"
   - Audio: "You have an appointment tomorrow at [time]. Press 1 to confirm, 2 to reschedule, or 3 to cancel."
   - Actions:
     - 1 → Playback: "confirmed" → Hangup
     - 2 → Call Extension: PJSIP/300 (Scheduling)
     - 3 → Call Extension: PJSIP/300 (Scheduling)

2. **Create Campaign**
   - Name: "Tomorrow's Appointments"
   - Agent Destination: IVR → "Appointment Reminder"
   - Concurrent Calls: 5
   - Record Calls: Yes

3. **Upload Patient List**
   ```csv
   phone_number,data
   +12125551001,{"patient":"John Doe","time":"2:00 PM","doctor":"Dr. Smith"}
   +12125551002,{"patient":"Jane Doe","time":"3:00 PM","doctor":"Dr. Jones"}
   ```

4. **Schedule Campaign**
   - Start campaign 24 hours before appointments
   - Monitor responses
   - Follow up on reschedule requests

---

### Scenario 5: Emergency Notifications

**Goal:** Quickly notify all employees of urgent situation.

**Steps:**

1. **Prepare Emergency Message**
   - Record urgent audio: "This is an emergency notification. Please call the office immediately."

2. **Create IVR**
   - Name: "Emergency Alert"
   - Upload audio file
   - Action: Press 1 to confirm receipt → Hangup

3. **Create Campaign**
   - Name: "Emergency Alert [Date]"
   - Agent Destination: IVR → "Emergency Alert"
   - Concurrent Calls: 20 (high for urgency)
   - Retry Times: 3
   - Retry Delay: 60 seconds

4. **Upload Employee List**
   - Import all employee phone numbers

5. **Start Immediately**
   - Start campaign
   - Monitor answered calls in real-time
   - Follow up with non-responders

---

## Troubleshooting Common Issues

### Campaign Not Starting

**Symptoms:** Click play but status stays "Stopped"

**Solutions:**
1. Check if Stasis app is running: `systemctl status ari-dialer`
2. Check Asterisk is running: `asterisk -rx "core show version"`
3. Review logs: `/var/www/html/adial/logs/`
4. Ensure there are pending numbers
5. Verify trunk configuration

### No Audio in IVR

**Symptoms:** Calls connect but no audio plays

**Solutions:**
1. Check audio file uploaded correctly
2. Verify file is in `/var/lib/asterisk/sounds/dialer/`
3. Test file format (should be 8000Hz WAV)
4. Check file permissions: `chmod 644 /var/lib/asterisk/sounds/dialer/*.wav`
5. Test playback manually in Asterisk

### All Calls Failing

**Symptoms:** Every call shows "Failed" status

**Solutions:**
1. Verify trunk is registered: `asterisk -rx "pjsip show endpoints"`
2. Check trunk configuration in campaign
3. Test trunk manually: Make test call from Asterisk CLI
4. Check phone number format
5. Verify SIP credentials

### Calls Connecting But Dropping Immediately

**Symptoms:** Calls answer then hang up right away

**Solutions:**
1. Check agent destination is correct
2. Verify extension exists and is registered
3. Review Asterisk dialplan
4. Check Stasis application logs
5. Ensure proper channel permissions

### Cannot Upload CSV

**Symptoms:** Upload fails or numbers don't import

**Solutions:**
1. Check file format (should be plain CSV)
2. Verify file size (< 10MB recommended)
3. Check CSV structure (phone_number column)
4. Remove special characters
5. Check file permissions on uploads directory

---

## Keyboard Shortcuts

- **Ctrl + /** - Open search
- **Ctrl + H** - Go to Dashboard
- **Ctrl + C** - Go to Campaigns
- **Ctrl + R** - Refresh current page

---

## Tips for Maximum Efficiency

1. **Use Browser Bookmarks**
   - Bookmark frequently used pages
   - Create bookmark folder for ARI Dialer

2. **Keep CDR Window Open**
   - Monitor calls in separate tab
   - Use dual monitors if available

3. **Create Campaign Templates**
   - Save common configurations
   - Reuse successful settings

4. **Regular Cleanup**
   - Delete old stopped campaigns
   - Archive old CDR records
   - Clean up unused IVR menus

5. **Schedule Campaigns**
   - Dial during optimal hours
   - Respect time zones
   - Avoid holidays/weekends

---

## Getting Help

### Documentation

- **Installation:** `INSTALL.md`
- **Features:** `FEATURES.md`
- **Quick Start:** `QUICKSTART.md`
- **Troubleshooting:** `TROUBLESHOOTING.md`

### Log Files

- **Application Logs:** `/var/www/html/adial/logs/`
- **Stasis Logs:** `journalctl -u ari-dialer -f`
- **Asterisk Logs:** `/var/log/asterisk/full`

### Support

- Check system logs for detailed error messages
- Review Asterisk documentation for ARI issues
- Consult troubleshooting guide

---

## Appendix

### Phone Number Formats

**Recommended E.164 Format:**
```
+[Country Code][Area Code][Number]

Examples:
US: +12125551234
UK: +442071234567
RU: +74951234567
```

### CSV Format Examples

**Simple List:**
```csv
+12125551001
+12125551002
+12125551003
```

**With Data:**
```csv
phone_number,data
+12125551001,{"name":"John Doe","account":"12345"}
+12125551002,{"name":"Jane Smith","account":"67890"}
```

### Audio File Conversion

**Using SOX (recommended):**
```bash
sox input.mp3 -r 8000 -c 1 output.wav
```

**Using FFmpeg:**
```bash
ffmpeg -i input.mp3 -ar 8000 -ac 1 output.wav
```

---

**Document Version:** 1.0
**Last Updated:** 2024-11-14
**Compatible with:** ARI Dialer v1.0+

---

For technical support and additional resources, please refer to the installation and troubleshooting documentation.
