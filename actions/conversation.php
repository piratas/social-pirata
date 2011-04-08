<?php
/**
 * Display a conversation in the browser
 *
 * PHP version 5
 *
 * @category Action
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2009, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

// XXX: not sure how to do paging yet,
// so set a 60-notice limit

require_once INSTALLDIR.'/lib/noticelist.php';

/**
 * Conversation tree in the browser
 *
 * @category Action
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 */
class ConversationAction extends Action
{
    var $id      = null;
    var $page    = null;
    var $notices = null;

    const MAX_NOTICES = 500;

    /**
     * Initialization.
     *
     * @param array $args Web and URL arguments
     *
     * @return boolean false if id not passed in
     */
    function prepare($args)
    {
        parent::prepare($args);
        $this->id = $this->trimmed('id');
        if (empty($this->id)) {
            return false;
        }
        $this->id = $this->id+0;
        $this->page = $this->trimmed('page');
        if (empty($this->page)) {
            $this->page = 1;
        }

        $cur = common_current_user();

        if (empty($cur)) {
            $profile = null;
        } else {
            $profile = $cur->getProfile();
        }

        $stream = new ConversationNoticeStream($this->id, $profile);

        $this->notices = $stream->getNotices(0, self::MAX_NOTICES, null, null);

        return true;
    }

    /**
     * Handle the action
     *
     * @param array $args Web and URL arguments
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        $this->showPage();
    }

    /**
     * Returns the page title
     *
     * @return string page title
     */
    function title()
    {
        // TRANS: Title for page with a conversion (multiple notices in context).
        return _('Conversation');
    }

    /**
     * Show content.
     *
     * Display a hierarchical unordered list in the content area.
     * Uses ConversationTree to do most of the heavy lifting.
     *
     * @return void
     */
    function showContent()
    {
        $tnl = new ThreadedNoticeList($this->notices, $this);

        $cnt = $tnl->show();
    }

    function isReadOnly()
    {
        return true;
    }
}
