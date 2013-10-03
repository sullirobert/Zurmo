<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2013 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2013. All rights reserved".
     ********************************************************************************/

    /**
     * Class for displaying metrics specific to a campaign
     */
    class CampaignOverallMetricsView extends MarketingMetricsView
    {
        /**
         * @var string
         */
        protected $formModelClassName = 'MarketingOverallMetricsForm';

        /**
         * The view's module class name.
         */
        public static function getModuleClassName()
        {
            return 'CampaignsModule';
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            $title  = Zurmo::t('CampaignsModule', 'Campaign Dashboard');
            return $title;
        }

        /**
         * @return string
         */
        public function renderContent()
        {
            $content  = ZurmoHtml::tag('h3', array(), Zurmo::t('CampaignsModule', 'What is going on with this campaign?'));
            $content .= $this->renderConfigureElementsContent();
            $content = ZurmoHtml::tag('div', array('class' => 'left-column full-width metrics-details ' . $this->getWrapperDivClass()), $content);
            $content .= $this->renderMetricsWrapperContent();
            return $content;
        }

        /**
         * @return string
         */
        protected function renderMetricsWrapperContent()
        {
            $cssClass = 'half marketing-graph';
            $content  = ZurmoHtml::tag('div', array('class' => $cssClass), $this->renderOverallListPerformanceContent());
            $content .= ZurmoHtml::tag('div', array('class' => $cssClass), $this->renderEmailsInThisListContent());
            return ZurmoHtml::tag('div', array('class' => 'graph-container clearfix'), $content);
        }

        /**
         * @return MarketingOverallMetricsConfigView
         */
        public function getConfigurationView()
        {
            return new MarketingOverallMetricsConfigView($this->resolveForm(), $this->params);
        }

        /**
         * Override to supply a campaign
         * @param string $type
         * @return ChartDataProvider
         */
        protected function resolveChartDataProvider($type)
        {
            assert('is_string($type)');
            $chartDataProvider = parent::resolveChartDataProvider($type);
            $chartDataProvider->setCampaign($this->params['relationModel']);
            return $chartDataProvider;
        }

        /**
         * @return string
         */
        protected function getWrapperDivClass()
        {
            return CampaignDetailsAndRelationsView::METRICS_PORTLET_CLASS;
        }

        /**
         * @return string
         */
        protected function getOverallListPerformanceTitle()
        {
            return Zurmo::t('MarketingModule', 'Overall Campaign Performance');
        }

        /**
         * @return string
         */
        protected function getEmailsInThisListTitle()
        {
            return Zurmo::t('MarketingModule', 'Emails in this Campaign');
        }
    }
?>