<?php
?>
<div class="col-12">
    <div class="filter-area-tab">
        <div class="light-bg border-20 ps-4 pe-4">
            <a class="filter-header border-20 d-block collapsed" data-bs-toggle="collapse" href="#collapseFilterHeader" role="button" aria-expanded="false"><span class="main-title fw-500 text-dark">Filter By</span></a>
            <div class="collapse border-top" id="collapseFilterHeader">
                <div class="pt-25 pb-30">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="filter-block pb-50 lg-pb-20">
                                <div class="filter-title fw-500 text-dark">Keyword or Title</div>
                                <form action="#" class="input-box position-relative">
                                    <input type="text" placeholder="Search by Keywords">
                                    <button><i class="bi bi-search"></i></button>
                                </form>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="filter-block pb-50 lg-pb-20">
                                <div class="filter-title fw-500 text-dark">Category</div>
                                <select class="nice-select bg-white">
                                    <option value="0">Web Design</option>
                                    <option value="1">Design & Creative </option>
                                    <option value="2">It & Development</option>
                                    <option value="3">Web & Mobile Dev</option>
                                    <option value="4">Writing</option>
                                    <option value="5">Sales & Marketing</option>
                                </select>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="filter-block pb-50 lg-pb-20">
                                <div class="filter-title fw-500 text-dark">Location</div>
                                <select class="nice-select bg-white">
                                    <option value="0">Washington DC</option>
                                    <option value="1">California, CA</option>
                                    <option value="2">New York</option>
                                    <option value="3">Miami</option>
                                </select>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="filter-block pb-50 lg-pb-20">
                                <div class="filter-title fw-500 text-dark">English Fluency</div>
                                <select class="nice-select bg-white">
                                    <option value="0">Basic</option>
                                    <option value="1">Conversational</option>
                                    <option value="2" selected>Fluent</option>
                                    <option value="3">Native/Bilingual</option>
                                </select>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="filter-block d-xl-flex pb-25">
                                <div class="filter-title fw-500 text-dark mt-1">Job Type : </div>
                                <div class="main-body ps-xl-4 flex-fill">
                                    <ul class="style-none filter-input">
                                        <li>
                                            <input type="checkbox" name="JobType" value="01">
                                            <label>Fulltime</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="JobType" value="02">
                                            <label>Hourly-Contract </label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="JobType" value="03">
                                            <label>Part-time (20hr/week)</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="JobType" value="04">
                                            <label>Fixed-Price</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="filter-block d-xl-flex pb-25">
                                <div class="filter-title fw-500 text-dark mt-1">Experience Level :</div>
                                <div class="main-body ps-xl-4 flex-fill">
                                    <ul class="style-none filter-input">
                                        <li>
                                            <input type="checkbox" name="Experience" value="01">
                                            <label>Beginner (1-3 yrs) </label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="Experience" value="02">
                                            <label>Intermediate (1-5 yrs) </label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="Experience" value="03">
                                            <label>Expert (1-10 yrs)</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="Experience" value="04">
                                            <label>Internship</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                        <div class="col-lg-4">
                            <div class="filter-block d-xl-flex pb-25">
                                <div class="filter-title fw-500 text-dark mt-1">Salary Range :</div>
                                <div class="main-body ps-xl-4 flex-fill">
                                    <div class="salary-slider">
                                        <div class="price-input d-flex align-items-center pt-5">
                                            <div class="field d-flex align-items-center">
                                                <input type="number" class="input-min" value="0" readonly>
                                            </div>
                                            <div class="pe-1 ps-1">-</div>
                                            <div class="field d-flex align-items-center">
                                                <input type="number" class="input-max" value="300" readonly>
                                            </div>
                                            <div class="currency ps-1">USD</div>
                                        </div>
                                        <div class="slider">
                                            <div class="progress"></div>
                                        </div>
                                        <div class="range-input mb-10">
                                            <input type="range" class="range-min" min="0" max="950" value="0" step="10">
                                            <input type="range" class="range-max" min="0" max="1000" value="300" step="10">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2 m-auto">
                            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30 md-mt-10">Apply Filter</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.filter header -->
        </div>
    </div>
    <!-- /.filter-area-tab -->
</div>
