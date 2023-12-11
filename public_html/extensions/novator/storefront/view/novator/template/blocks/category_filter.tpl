<div class="offcanvas-xl offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
    <div class="offcanvas-body p-0 sticky-xl-top">
        <div id="ecom-filter" class="show collapse collapse-horizontal">
            <div class="ecom-filter">
                <div class="card">
                    <div class="card-header d-flex d-xl-none align-items-center justify-content-between">
                        <h5>Filter</h5>
                        <button type="button" class="btn btn-danger btn-icon" data-bs-dismiss="offcanvas" data-bs-target="#offcanvas_mail_filter" aria-label="Close">
                            <i class="bi bi-x"></i></button>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 px-0 py-2">
                                <a class="btn border-0 px-0 text-start w-100 pb-0" data-bs-toggle="collapse"
                                   href="#filtercollapse1">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Category
                                </a>
                                <div class="collapse show" id="filtercollapse1">
                                    <div>
                                        <?php foreach($category_tree as $cat){
                                            $inputId =  'category'. $cat['category_id'];
                                            ?>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="<?php echo $inputId; ?>" value="<?php echo $cat['category_id']; ?>">
                                            <label class="form-check-label d-block" for="<?php echo $inputId; ?>"><?php echo $cat['name']?> <span
                                                    class="float-end">(<?php echo $cat['product_count']?>)</span></label>
                                        </div>
                                        <?php
                                        if(!$cat['children']){ continue; }
                                            foreach($cat['children'] as $child){ ?>
                                                <div class="form-check my-2">
                                                    <label class="form-check-label d-block" > - <?php echo $child['name']?> <span
                                                                class="float-end">(<?php echo $child['product_count']?>)</span></label>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a class="btn border-0 px-0 text-start w-100 pb-0" data-bs-toggle="collapse"
                                   href="#filtercollapse2">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Ratings
                                </a>
                                <div class="collapse show" id="filtercollapse2">
                                    <div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="radio" name="ratings" id="categoryfilter1"
                                                   value="option1">
                                            <label class="form-check-label d-block" for="categoryfilter1"><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i>4.5 & up <span
                                                    class="float-end">(12)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="radio" name="ratings" id="categoryfilter2"
                                                   value="option2">
                                            <label class="form-check-label d-block" for="categoryfilter2"><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i>4.0 & up <span
                                                    class="float-end">(12)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="radio" name="ratings" id="categoryfilter3"
                                                   value="option3">
                                            <label class="form-check-label d-block" for="categoryfilter3"><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i>3.5 & up <span
                                                    class="float-end">(12)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="radio" name="ratings" id="categoryfilter4"
                                                   value="option1">
                                            <label class="form-check-label d-block" for="categoryfilter4"><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i><i
                                                    class="bi bi-star-fill text-warning fs-6 me-1"></i>3.0 & up <span
                                                    class="float-end">(12)</span></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a class="btn border-0 px-0 text-start w-100 pb-2" data-bs-toggle="collapse"
                                   href="#filtercollapse3">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Brand
                                </a>
                                <div class="collapse show" id="filtercollapse3">
                                    <div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="brandfilter1" value="option1">
                                            <label class="form-check-label d-block" for="brandfilter1">Adidas <span
                                                    class="float-end">(18)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="brandfilter2" value="option1">
                                            <label class="form-check-label d-block" for="brandfilter2">Nick <span
                                                    class="float-end">(12)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="brandfilter3" value="option1">
                                            <label class="form-check-label d-block" for="brandfilter3">Jacek & Co <span
                                                    class="float-end">(23)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="brandfilter4" value="option1">
                                            <label class="form-check-label d-block" for="brandfilter4">My Shooed <span
                                                    class="float-end">(67)</span></label>
                                        </div>
                                        <div class="form-check my-2">
                                            <input class="form-check-input" type="checkbox" id="brandfilter5" value="option1">
                                            <label class="form-check-label d-block" for="brandfilter5">Florida Fox <span
                                                    class="float-end">(34)</span></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a class="btn border-0 px-0 text-start w-100 pb-0" data-bs-toggle="collapse"
                                   href="#filtercollapse4">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Price
                                </a>
                                <div class="collapse show" id="filtercollapse4">
                                    <input type="range" class="form-range mt-3 mb-2">
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control" value="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="form-control" value="$200">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0 py-2">
                                <a class="btn border-0 px-0 text-start w-100 pb-2" data-bs-toggle="collapse"
                                   href="#filtercollapse5">
                                    <div class="float-end"><i class="bi bi-chevron-down"></i></div>
                                    Size
                                </a>
                                <div class="collapse show" id="filtercollapse5">
                                    <div>
                                        <input type="range" class="form-range mt-3 mb-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="text" class="form-control" value="0">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control" value="$200">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>