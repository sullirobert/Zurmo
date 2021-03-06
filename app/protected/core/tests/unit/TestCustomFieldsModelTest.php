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

    class TestCustomFieldsModelTest extends BaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            $multiSelectValues = array(
                'Multi 1',
                'Multi 2',
                'Multi 3',
            );
            $customFieldData = CustomFieldData::getByName('MultipleSomethings');
            $customFieldData->serializedData = serialize($multiSelectValues);
            $save = $customFieldData->save();
            assert('$save'); // Not Coding Standard

            $tagCloudValues = array(
                'Cloud 1',
                'Cloud 2',
                'Cloud 3',
            );
            $customFieldData = CustomFieldData::getByName('TagCloud');
            $customFieldData->serializedData = serialize($tagCloudValues);
            $save = $customFieldData->save();
            assert('$save'); // Not Coding Standard
        }

        public function testMultiSelectAndTagCloudRelationships()
        {
            $testModel = new TestCustomFieldsModel();

            $customFieldValue = new CustomFieldValue();
            $customFieldValue->value = 'Multi 1';
            $testModel->multipleSomethings->values->add($customFieldValue);

            $customFieldValue = new CustomFieldValue();
            $customFieldValue->value = 'Multi 3';
            $testModel->multipleSomethings->values->add($customFieldValue);

            $customFieldValue = new CustomFieldValue();
            $customFieldValue->value = 'Cloud 2';
            $testModel->tagCloud->values->add($customFieldValue);

            $customFieldValue = new CustomFieldValue();
            $customFieldValue->value = 'Cloud 3';
            $testModel->tagCloud->values->add($customFieldValue);

            //Should this be 2? https://www.pivotaltracker.com/story/show/25407631
            $this->assertEquals(2, count($testModel->multipleSomethings->values));
            $this->assertEquals('Multi 1', $testModel->multipleSomethings->values[0]->value);
            $this->assertEquals('Multi 3', $testModel->multipleSomethings->values[1]->value);

            //Should this be 2? https://www.pivotaltracker.com/story/show/25407631
            $this->assertEquals(2, count($testModel->tagCloud->values));
            $this->assertEquals('Cloud 2', $testModel->tagCloud->values[0]->value);
            $this->assertEquals('Cloud 3', $testModel->tagCloud->values[1]->value);
        }
    }
